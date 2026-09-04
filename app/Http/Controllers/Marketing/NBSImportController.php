<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\Customer;
use App\Models\Book;
use App\Models\Company;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class NBSImportController extends Controller
{
    public function index()
    {
        // Pass top-level companies for the cascading company/branch dropdowns
        $companies = Company::whereNull('parent_id')
            ->where('is_inactive', false)
            ->orderBy('company_name', 'asc')
            ->get(['company_id', 'company_name']);

        return view('marketing.direct-sales.nbs-import', [
            'title' => 'NBS PO Import',
            'sidebar' => 'marketing',
            'companies' => $companies,
        ]);
    }

    public function downloadTemplate(Request $request)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('PO Template');
        
        // Clean headers including Discount, Discount Type, Cancellation Date, Remarks & Address
        $headers = [
            'PO Number',         // A
            'PO Date',           // B
            'Cancellation Date', // C
            'Qty',               // D
            'Book Article',      // E
            'NBS Branch',        // F
            'Discount',          // G
            'Discount Type',     // H
            'Remarks',           // I
            'Address',           // J
        ];
        
        foreach ($headers as $colIndex => $header) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 1);
            $sheet->setCellValue($colLetter . '1', $header);
        }

        // Header Styling (Claretian Red Theme, Centered, Bold White text)
        $sheet->getRowDimension(1)->setRowHeight(28);
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'C1121F'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ];
        $sheet->getStyle('A1:J1')->applyFromArray($headerStyle);

        // Column Widths
        $colWidths = [
            'A' => 22,
            'B' => 15,
            'C' => 18,
            'D' => 12,
            'E' => 25,
            'F' => 30,
            'G' => 15,
            'H' => 18,
            'I' => 25,
            'J' => 35,
        ];
        foreach ($colWidths as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }

        // --- NBS Branch dropdown from selected branch's sub-branches (column F) ---
        $branchId = $request->query('branch_id');
        $subBranches = [];

        if ($branchId) {
            $selectedBranch = Company::find($branchId);
            if ($selectedBranch) {
                $subBranches = $selectedBranch->branches()
                    ->where('is_inactive', false)
                    ->orderBy('company_name', 'asc')
                    ->pluck('company_name')
                    ->toArray();

                if (empty($subBranches)) {
                    $subBranches = [$selectedBranch->company_name];
                }
            }
        }

        // Add Sample Data Row (Row 2) as a visual guide
        $sampleBranch = !empty($subBranches) ? $subBranches[0] : 'NBS Sample Branch';
        $sheet->setCellValue('A2', 'PO-2026-001');
        $sheet->setCellValue('B2', date('Y-m-d'));
        $sheet->setCellValue('C2', date('Y-m-d', strtotime('+30 days')));
        $sheet->setCellValue('D2', 10);
        $sheet->setCellValue('E2', 'ART-1001');
        $sheet->setCellValue('F2', $sampleBranch);
        $sheet->setCellValue('G2', 10);
        $sheet->setCellValue('H2', '%');
        $sheet->setCellValue('I2', 'Sample Order Remarks');
        $sheet->setCellValue('J2', 'Sample Delivery Address, Quezon City');

        if (!empty($subBranches)) {
            $branchSheet = $spreadsheet->createSheet();
            $branchSheet->setTitle('NBSBranchesList');
            $branchSheet->setSheetState(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::SHEETSTATE_HIDDEN);

            foreach ($subBranches as $idx => $branchName) {
                $branchSheet->setCellValue('A' . ($idx + 1), $branchName);
            }

            $totalBranches = count($subBranches);

            // NBS Branch dropdown validation (Column F)
            for ($row = 2; $row <= 200; $row++) {
                $validation = $sheet->getCell('F' . $row)->getDataValidation();
                $validation->setType(DataValidation::TYPE_LIST);
                $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
                $validation->setAllowBlank(true);
                $validation->setShowInputMessage(true);
                $validation->setShowErrorMessage(true);
                $validation->setShowDropDown(true);
                $validation->setErrorTitle('Input error');
                $validation->setError('Value is not in list');
                $validation->setPromptTitle('Pick a Branch');
                $validation->setPrompt('Please pick an NBS branch from the dropdown list');
                $validation->setFormula1('NBSBranchesList!$A$1:$A$' . $totalBranches);
            }
        }

        // Discount Type dropdown validation (Column H: % or ₱)
        for ($row = 2; $row <= 200; $row++) {
            $validationType = $sheet->getCell('H' . $row)->getDataValidation();
            $validationType->setType(DataValidation::TYPE_LIST);
            $validationType->setErrorStyle(DataValidation::STYLE_INFORMATION);
            $validationType->setAllowBlank(true);
            $validationType->setShowDropDown(true);
            $validationType->setErrorTitle('Input error');
            $validationType->setError('Select % or ₱');
            $validationType->setPromptTitle('Discount Type');
            $validationType->setPrompt('Select % (Percentage) or ₱ (Amount)');
            $validationType->setFormula1('"%,₱"');
        }

        $spreadsheet->setActiveSheetIndex(0);

        $writer = new Xlsx($spreadsheet);
        
        $response = response()->stream(function() use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="nbs_po_bulk_template.xlsx"',
            'Cache-Control' => 'max-age=0',
        ]);
        
        return $response;
    }

    public function process(Request $request)
    {
        $request->validate([
            'po_file' => 'required|file'
        ]);

        $file = $request->file('po_file');
        $path = $file->getRealPath();
        
        try {
            $spreadsheet = IOFactory::load($path);
            $sheet = $spreadsheet->getActiveSheet();
            $lines = $sheet->toArray();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error reading import file: ' . $e->getMessage());
        }

        if (empty($lines)) {
            return redirect()->back()->with('error', 'The file is empty.');
        }

        // Clean headers and handle BOM
        $header = array_map(function($val) {
            return trim(str_replace("\ufeff", '', $val ?? ''));
        }, $lines[0]);

        // Detect if this is the bulk upload template or the old HD/DT format.
        $isBulkTemplate = false;
        if (isset($header[0]) && (strtolower($header[0]) === 'po number' || strtolower($header[0]) === 'nbs branch')) {
            $isBulkTemplate = true;
        }

        if ($isBulkTemplate) {
            return $this->processBulkTemplate($lines);
        } else {
            return $this->processLegacyFormat($lines);
        }
    }

    private function processBulkTemplate($lines)
    {
        // Lowercase and trim headers for mapping
        $headers = array_map(function($h) {
            return strtolower(trim(str_replace("\ufeff", '', $h ?? '')));
        }, $lines[0]);

        $findCol = function($candidates) use ($headers) {
            foreach ($candidates as $cand) {
                $idx = array_search(strtolower($cand), $headers);
                if ($idx !== false) return $idx;
            }
            return false;
        };

        $colIndices = [
            'po_number'         => $findCol(['po number', 'po_number', 'po #', 'po']),
            'po_date'           => $findCol(['po date', 'po_date', 'date']),
            'cancellation_date' => $findCol(['cancellation date', 'cancellation_date', 'cancel date', 'cancellation']),
            'qty'               => $findCol(['qty', 'quantity']),
            'book_article'      => $findCol(['book article', 'article', 'barcode', 'sku', 'isbn', 'book']),
            'nbs_branch'        => $findCol(['nbs branch', 'branch']),
            'discount'          => $findCol(['discount', 'discount value', 'disc', 'discount_val', 'discount amount']),
            'discount_type'     => $findCol(['discount type', 'disc type', 'discount_type', 'type']),
            'remarks'           => $findCol(['remarks', 'remark', 'notes', 'instruction', 'instructions']),
            'address'           => $findCol(['address', 'shipping address', 'delivery address', 'ship address', 'location']),
        ];

        // Validate critical fields are mapped
        if ($colIndices['po_number'] === false || $colIndices['qty'] === false || $colIndices['book_article'] === false) {
            return redirect()->back()->with('error', 'Critical headers are missing in the template. Please make sure PO Number, Qty, and Book Article columns exist.');
        }

        $getValue = function($row, $key) use ($colIndices) {
            if (!isset($colIndices[$key])) return '';
            $idx = $colIndices[$key];
            if ($idx === false || !isset($row[$idx])) {
                return '';
            }
            return trim($row[$idx] ?? '');
        };

        $orders = [];
        $missingBooks = [];
        $lastPoNumber = '';

        for ($i = 1; $i < count($lines); $i++) {
            $row = $lines[$i];
            if (empty(array_filter($row))) continue;

            $poNumber = $getValue($row, 'po_number');
            if (!$poNumber && !empty($lastPoNumber)) {
                $poNumber = $lastPoNumber;
            }
            if (!$poNumber) {
                continue;
            }
            $lastPoNumber = $poNumber;

            $bookArticle = $getValue($row, 'book_article');
            $qty = (float)$getValue($row, 'qty');
            if ($qty <= 0) continue;

            // Extract Discount & Discount Type
            $rawDiscount = $getValue($row, 'discount');
            $rawDiscType = strtolower($getValue($row, 'discount_type'));

            $discVal = 0;
            $discType = 'percentage';

            if ($rawDiscount !== '') {
                if (str_contains($rawDiscount, '%')) {
                    $discVal = (float) str_replace('%', '', $rawDiscount);
                    $discType = 'percentage';
                } elseif (str_contains(strtolower($rawDiscount), '₱') || str_contains(strtolower($rawDiscount), 'php')) {
                    $discVal = (float) preg_replace('/[^\d.]/', '', $rawDiscount);
                    $discType = 'amount';
                } else {
                    $discVal = (float) preg_replace('/[^\d.]/', '', $rawDiscount);
                    if ($rawDiscType === 'amount' || $rawDiscType === '₱' || $rawDiscType === 'php' || $rawDiscType === 'fixed' || $rawDiscType === 'pesos') {
                        $discType = 'amount';
                    } else {
                        $discType = 'percentage';
                    }
                }
            }

            // Find BookIndex or Book by article, barcode, or nbs_barcode
            $bookIndex = null;
            $book = null;

            if ($bookArticle !== '') {
                // 1. Check BookIndex by article, barcode, nbs_barcode, or index_value
                $bookIndex = \App\Models\BookIndex::with('book')
                    ->where('article', $bookArticle)
                    ->orWhere('barcode', $bookArticle)
                    ->orWhere('nbs_barcode', $bookArticle)
                    ->orWhere('index_value', $bookArticle)
                    ->first();

                if ($bookIndex) {
                    $book = $bookIndex->book;
                } else {
                    // 2. Check Book by article, barcode, nbs_barcode, or sku
                    $book = Book::where('article', $bookArticle)
                        ->orWhere('barcode', $bookArticle)
                        ->orWhere('nbs_barcode', $bookArticle)
                        ->orWhere('sku', $bookArticle)
                        ->first();
                }
            }

            if (!$bookIndex && !$book) {
                $missingBooks[$bookArticle ?: 'Empty Article'] = $bookArticle ?: 'Empty Article';
            }

            if (!isset($orders[$poNumber])) {
                $orders[$poNumber] = [
                    'po_number'         => $poNumber,
                    'po_date'           => $getValue($row, 'po_date'),
                    'cancellation_date' => $getValue($row, 'cancellation_date'),
                    'nbs_branch'        => $getValue($row, 'nbs_branch'),
                    'remarks'           => $getValue($row, 'remarks'),
                    'address'           => $getValue($row, 'address'),
                    'items'             => []
                ];
            } else {
                if (empty($orders[$poNumber]['remarks']) && $getValue($row, 'remarks')) {
                    $orders[$poNumber]['remarks'] = $getValue($row, 'remarks');
                }
                if (empty($orders[$poNumber]['address']) && $getValue($row, 'address')) {
                    $orders[$poNumber]['address'] = $getValue($row, 'address');
                }
            }

            // Determine unit price and description
            if ($bookIndex) {
                $unitPrice = ($bookIndex->price && $bookIndex->price > 0) ? (float)$bookIndex->price : ($book ? (float)$book->price : 0);
                $description = $bookIndex->display_name;
            } else {
                $unitPrice = $book ? (float)$book->price : 0;
                $description = $book ? $book->name : '';
            }

            $orders[$poNumber]['items'][] = [
                'book_id'        => $book ? $book->id : null,
                'book_index_id'  => $bookIndex ? $bookIndex->id : null,
                'description'    => $description,
                'qty'            => $qty,
                'price'          => $unitPrice,
                'discount_value' => $discVal,
                'discount_type'  => $discType,
            ];
        }

        // If any books are missing, stop and notify user
        if (!empty($missingBooks)) {
            $list = implode(', ', array_keys($missingBooks));
            Log::warning("NBS Import Failed: Missing books: " . $list);
            return redirect()->back()->with('error', 'The following items were not found in your Master Registry or Index Registry: ' . $list . '. Please add them exactly as named or coded in the import file before importing.');
        }

        if (empty($orders)) {
            Log::warning("NBS Import: No orders were detected.");
            return redirect()->back()->with('error', 'No valid NBS PO data found in the file.');
        }

        // STOCK VALIDATION
        $stockIssues = [];
        foreach ($orders as $poNum => $poData) {
            if (empty($poData['items'])) continue;
            
            foreach ($poData['items'] as $item) {
                if (!empty($item['book_index_id'])) {
                    $index = \App\Models\BookIndex::find($item['book_index_id']);
                    $availableStock = $index ? $index->main_stock : 0;
                    if (!$index || $availableStock < $item['qty']) {
                        $itemName = $index ? $index->display_name : "Book Index ID #{$item['book_index_id']}";
                        $stockIssues[] = "PO #$poNum: $itemName (Available: $availableStock pcs, Requested: {$item['qty']} pcs)";
                    }
                } else {
                    $book = Book::find($item['book_id']);
                    $availableStock = $book ? $book->stock : 0;
                    if (!$book || $availableStock < $item['qty']) {
                        $bookName = $book ? $book->name : "Book ID #{$item['book_id']}";
                        $stockIssues[] = "PO #$poNum: $bookName (Available: $availableStock pcs, Requested: {$item['qty']} pcs)";
                    }
                }
            }
        }

        if (!empty($stockIssues)) {
            Log::warning("NBS Import: Insufficient stock - " . implode(' | ', $stockIssues));
            return redirect()->back()->with('error', 'Insufficient stock for the following items in NBS import:<br>• ' . implode('<br>• ', $stockIssues));
        }

        // Process orders into the database
        DB::beginTransaction();
        try {
            // Determine Parent Company & Branch names based on user selection or defaults
            $parentCompanyName = 'National Book Store';
            $defaultBranchName = 'Abacus';

            if (request('branch_id')) {
                $bComp = Company::with('parent')->find(request('branch_id'));
                if ($bComp) {
                    $defaultBranchName = $bComp->company_name;
                    if ($bComp->parent) {
                        $parentCompanyName = $bComp->parent->company_name;
                    }
                }
            } elseif (request('company_id')) {
                $cComp = Company::find(request('company_id'));
                if ($cComp) {
                    $parentCompanyName = $cComp->company_name;
                }
            }

            // Find branch company record if available to get account_number
            $branchCompany = Company::where('company_name', $defaultBranchName)->first();
            $accountNo = $branchCompany ? $branchCompany->account_number : ('CUST-NBS-' . strtoupper(\Illuminate\Support\Str::slug($defaultBranchName)));

            // 1. Search customer by account_number FIRST to prevent duplicate key constraint violations
            $customer = Customer::where('account_number', $accountNo)->first();

            if (!$customer) {
                // 2. Search customer by branch or company name if not found by account_number
                $customer = Customer::where('customer_name', 'like', "%{$defaultBranchName}%")
                    ->orWhere('company_name', 'like', "%{$parentCompanyName}%")
                    ->first();
            }

            if (!$customer) {
                // 3. Create new customer if neither account_number nor name matched
                $customer = Customer::create([
                    'company_name'   => $parentCompanyName,
                    'customer_name'  => $defaultBranchName,
                    'account_number' => $accountNo,
                    'customer_type'  => 'business',
                ]);
            } else {
                // Update names safely without conflicting on existing account numbers
                $updateData = [
                    'company_name'   => $parentCompanyName,
                    'customer_name'  => $defaultBranchName,
                ];

                if (empty($customer->account_number) && $accountNo) {
                    $acctHolder = Customer::where('account_number', $accountNo)->first();
                    if (!$acctHolder) {
                        $updateData['account_number'] = $accountNo;
                    }
                }

                $customer->update($updateData);
            }

            $createdCount = 0;
            foreach ($orders as $poNum => $poData) {
                if (empty($poData['items'])) continue;

                $branchRepresentative = !empty($poData['nbs_branch']) ? $poData['nbs_branch'] : $defaultBranchName;
                $remarksStr = !empty($poData['remarks']) ? $poData['remarks'] : null;
                $addressStr = !empty($poData['address']) ? $poData['address'] : null;
                $cancelDate = !empty($poData['cancellation_date']) ? $poData['cancellation_date'] : null;

                // Create SalesOrder in "pending_mkt_approval" status for Marketing Approval Queue
                $soData = [
                    'customer_id'             => $customer->customer_id,
                    'customer_representative' => $branchRepresentative,
                    'so_number'               => 'SO-NBS-' . $poNum . '-' . date('His'),
                    'type'                    => 'direct_consignment',
                    'transaction_type'        => 'direct_consignment',
                    'ref_number'              => $poNum,
                    'remarks'                 => $remarksStr,
                    'shipping_address'        => $addressStr,
                    'status'                  => 'pending_mkt_approval',
                    'prepared_by'             => auth()->id(),
                    'total_amount'            => 0,
                ];

                if ($cancelDate && \Illuminate\Support\Facades\Schema::hasColumn('sales_orders', 'cancellation_date')) {
                    $soData['cancellation_date'] = $cancelDate;
                }

                $so = SalesOrder::create($soData);

                $totalAmount = 0;
                
                foreach ($poData['items'] as $item) {
                    $unitPrice = $item['price'];
                    $qty = $item['qty'];
                    $grossSubtotal = $qty * $unitPrice;

                    $discVal = (float)($item['discount_value'] ?? 0);
                    $discType = $item['discount_type'] ?? 'percentage';
                    
                    $discAmount = 0;
                    if ($discVal > 0) {
                        if ($discType === 'percentage') {
                            $discAmount = $grossSubtotal * ($discVal / 100);
                        } else {
                            $discAmount = $discVal;
                        }
                        $discAmount = min($grossSubtotal, max(0, $discAmount));
                    }
                    
                    $subtotal = max(0, $grossSubtotal - $discAmount);

                    $soItem = SalesOrderItem::create([
                        'sales_order_id'  => $so->id,
                        'book_id'         => $item['book_id'],
                        'book_index_id'   => $item['book_index_id'] ?? null,
                        'quantity'        => $qty,
                        'price'           => $unitPrice,
                        'discount_value'  => $discVal,
                        'discount_type'   => $discType,
                        'discount_amount' => $discAmount,
                        'subtotal'        => $subtotal,
                    ]);
                    $totalAmount += $subtotal;
                }
                
                $so->update(['total_amount' => $totalAmount]);
                \App\Services\StockDeductionService::deductForSalesOrder($so);
                $createdCount++;
            }

            DB::commit();
            return redirect()->route('marketing.approval-queue')->with('success', "Successfully imported $createdCount NBS Purchase Orders. They have been routed to the Marketing Approval Queue for review.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('NBS Bulk Import Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Critical Error during bulk import: ' . $e->getMessage());
        }
    }

    private function processLegacyFormat($data)
    {
        $orders = [];
        $currentPO = null;
        $missingBooks = [];

        foreach ($data as $rowIndex => $row) {
            // Clean the row and handle BOM
            $row = array_map(function($val) {
                return trim(str_replace("\ufeff", '', $val ?? ''));
            }, $row);
            
            if (empty(array_filter($row))) continue;

            // PO Header (HD)
            if ($row[0] === 'HD') {
                $currentPO = $row[1] ?? '';
                if (!$currentPO) continue;
                
                $orders[$currentPO] = [
                    'po_number' => $currentPO,
                    'po_date' => $row[2] ?? '',
                    'vendor_code' => $row[3] ?? '',
                    'remarks' => ($row[6] ?? '') . ' ' . ($row[7] ?? ''),
                    'items' => []
                ];
                continue;
            }

            // PO Item Detail (DT)
            if ($row[0] === 'DT') {
                $linePO = $row[2] ?? '';
                if (!$currentPO || $linePO !== $currentPO) {
                    $currentPO = $linePO;
                    if (!isset($orders[$currentPO])) {
                         $orders[$currentPO] = [
                            'po_number' => $currentPO,
                            'items' => []
                         ];
                    }
                }

                $description = $row[6] ?? '';
                $gtin = $row[5] ?? '';
                
                $bookIndex = null;
                $book = null;

                if (!empty($gtin)) {
                    $bookIndex = \App\Models\BookIndex::with('book')
                        ->where('nbs_barcode', $gtin)
                        ->orWhere('barcode', $gtin)
                        ->orWhere('article', $gtin)
                        ->first();
                }

                if (!$bookIndex && !empty($description)) {
                    $bookIndex = \App\Models\BookIndex::with('book')
                        ->where('index_value', $description)
                        ->orWhereHas('book', function($q) use ($description) {
                            $q->where('name', $description);
                        })
                        ->first();
                }

                if ($bookIndex) {
                    $book = $bookIndex->book;
                } else {
                    $book = Book::where('name', $description)
                        ->orWhere('name', 'like', $description . '%')
                        ->orWhere('nbs_barcode', $gtin)
                        ->orWhere('barcode', $gtin)
                        ->orWhere('article', $gtin)
                        ->first();
                }
                
                if (!$bookIndex && !$book) {
                    $missingBooks[$description ?: $gtin] = $description ?: $gtin;
                }

                $discountPercent = isset($row[14]) ? (float)$row[14] : 0;

                $price = (float)($row[9] ?? 0);
                if ($price <= 0) {
                    if ($bookIndex) {
                        $price = ($bookIndex->price && $bookIndex->price > 0) ? (float)$bookIndex->price : ($book ? (float)$book->price : 0);
                    } else {
                        $price = $book ? (float)$book->price : 0;
                    }
                }

                $orders[$currentPO]['items'][] = [
                    'line_item'        => $row[1],
                    'description'      => $bookIndex ? $bookIndex->display_name : ($book ? $book->name : $description),
                    'gtin'             => $gtin,
                    'qty'              => (float)($row[7] ?? 0),
                    'price'            => $price,
                    'discount_percent' => $discountPercent,
                    'book_id'          => $book ? $book->id : null,
                    'book_index_id'    => $bookIndex ? $bookIndex->id : null,
                ];
            }
        }

        if (!empty($missingBooks)) {
            $list = implode(', ', array_keys($missingBooks));
            Log::warning("NBS Import Failed: Missing items: " . $list);
            return redirect()->back()->with('error', 'The following items were not found in your Master Registry or Index Registry: ' . $list . '. Please add them before importing.');
        }

        if (empty($orders)) {
            Log::warning("NBS Import: No orders were detected.");
            return redirect()->back()->with('error', 'No valid NBS PO data (HD/DT rows) found in the file.');
        }

        // STOCK VALIDATION
        $stockIssues = [];
        foreach ($orders as $poNum => $poData) {
            if (empty($poData['items'])) continue;
            
            foreach ($poData['items'] as $item) {
                if (!empty($item['book_index_id'])) {
                    $index = \App\Models\BookIndex::find($item['book_index_id']);
                    $availableStock = $index ? $index->main_stock : 0;
                    if (!$index || $availableStock < $item['qty']) {
                        $itemName = $index ? $index->display_name : "Book Index ID #{$item['book_index_id']}";
                        $stockIssues[] = "PO #$poNum: $itemName (Available: $availableStock pcs, Requested: {$item['qty']} pcs)";
                    }
                } else {
                    $book = Book::find($item['book_id']);
                    $availableStock = $book ? $book->stock : 0;
                    if (!$book || $availableStock < $item['qty']) {
                        $bookName = $book ? $book->name : "Book ID #{$item['book_id']}";
                        $stockIssues[] = "PO #$poNum: $bookName (Available: $availableStock pcs, Requested: {$item['qty']} pcs)";
                    }
                }
            }
        }

        if (!empty($stockIssues)) {
            Log::warning("NBS Import: Insufficient stock - " . implode(' | ', $stockIssues));
            return redirect()->back()->with('error', 'Insufficient stock for the following items in NBS import:<br>• ' . implode('<br>• ', $stockIssues));
        }

        // Process orders into database
        DB::beginTransaction();
        try {
            $abacusCustomer = Customer::where('company_name', 'like', '%abacus%')
                ->orWhere('customer_name', 'like', '%abacus%')
                ->first();

            if (!$abacusCustomer) {
                $abacusCustomer = Customer::create([
                    'customer_name'  => 'abacus',
                    'company_name'   => 'abacus',
                    'account_number' => 'CUST-NBS-ABACUS',
                    'customer_type'  => 'business',
                ]);
            }

            $createdCount = 0;
            foreach ($orders as $poNum => $poData) {
                if (empty($poData['items'])) continue;

                $vendorCode = $poData['vendor_code'] ?? '';
                $customer = Customer::where('account_number', $vendorCode)->first();
                
                $so = SalesOrder::create([
                    'customer_id'        => $customer ? $customer->customer_id : $abacusCustomer->customer_id, 
                    'so_number'          => 'SO-NBS-' . $poNum . '-' . date('His'),
                    'type'               => 'direct_consignment',
                    'transaction_type'   => 'direct_consignment',
                    'ref_number'         => $poNum,
                    'remarks'            => ($poData['remarks'] ?? '') . ($customer ? '' : " (Vendor Code: $vendorCode)"),
                    'status'             => 'draft',
                    'prepared_by'        => auth()->id(),
                    'discount_percentage'=> $poData['items'][0]['discount_percent'] ?? 0,
                    'total_amount'       => 0,
                ]);

                $totalAmount = 0;
                foreach ($poData['items'] as $item) {
                    $subtotal = $item['qty'] * $item['price'];
                    
                    SalesOrderItem::create([
                        'sales_order_id' => $so->id,
                        'book_id'        => $item['book_id'],
                        'book_index_id'  => $item['book_index_id'] ?? null,
                        'quantity'       => $item['qty'],
                        'price'          => $item['price'],
                        'subtotal'       => $subtotal,
                    ]);
                    $totalAmount += $subtotal;
                }
                
                $so->update(['total_amount' => $totalAmount]);
                \App\Services\StockDeductionService::deductForSalesOrder($so);
                $createdCount++;
            }

            DB::commit();
            return redirect()->route('marketing.sales-orders.list')->with('success', "Successfully imported $createdCount NBS Purchase Orders.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('NBS Import Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Critical Error during import: ' . $e->getMessage());
        }
    }

    public function viewReceipt($id)
    {
        $order = SalesOrder::with(['customer', 'items.book', 'items.bookIndex.book', 'preparedBy'])->findOrFail($id);
        
        return view('marketing.direct-sales.nbs-consignment-receipt', [
            'order' => $order,
            'title' => 'Consignment Delivery Receipt',
            'sidebar' => 'marketing'
        ]);
    }
}
