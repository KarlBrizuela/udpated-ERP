<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Only load top-level companies (where parent_id is null) in the main list
        $companies = Company::whereNull('parent_id')->orderBy('company_name', 'asc')->get();
        $parentCompanies = Company::orderBy('company_name', 'asc')->get();

        return view('marketing.companies', [
            'companies' => $companies,
            'parentCompanies' => $parentCompanies,
            'title' => 'Company Management',
            'role' => 'Marketing Manager',
            'sidebar' => 'marketing'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:companies,company_id',
            'account_number' => 'nullable|string|unique:companies,account_number',
            'mobile' => 'nullable|string',
            'main_email' => 'nullable|email',
            'shipping_address' => 'nullable|string',
            'is_inactive' => 'nullable|boolean',
        ]);

        if (empty($validated['account_number'])) {
            $validated['account_number'] = null;
        }

        // Copy parent contact/address fields if none are provided
        if (!empty($validated['parent_id'])) {
            $parent = Company::find($validated['parent_id']);
            if ($parent) {
                if (empty($validated['mobile'])) {
                    $validated['mobile'] = $parent->mobile;
                }
                if (empty($validated['main_email'])) {
                    $validated['main_email'] = $parent->main_email;
                }
                if (empty($validated['shipping_address'])) {
                    $validated['shipping_address'] = $parent->shipping_address;
                }
            }
        }

        $company = Company::create($validated);

        return response()->json([
            'message' => 'Company created successfully',
            'company' => $company
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function show(Company $company)
    {
        return response()->json($company);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function edit(Company $company)
    {
        return response()->json($company->load('branches'));
    }

    /**
     * Return a company's direct branches as JSON (for cascade dropdowns).
     *
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBranches(Company $company)
    {
        $branches = $company->branches()
            ->where('is_inactive', false)
            ->orderBy('company_name', 'asc')
            ->get(['company_id', 'company_name', 'account_number']);

        return response()->json($branches);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Company $company)
    {
        $user = auth()->user();
        if (!($user->isSuperAdmin() || ($user->hasPermission('marketing.customers')))) {
            return response()->json(['message' => 'Unauthorized action. Only Super Admin or Marketing can edit companies.'], 403);
        }

        if ($request->parent_id && $request->parent_id == $company->company_id) {
            return response()->json(['message' => 'A company/branch cannot be its own parent.'], 422);
        }

        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:companies,company_id',
            'account_number' => 'nullable|string|unique:companies,account_number,' . $company->company_id . ',company_id',
            'mobile' => 'nullable|string',
            'main_email' => 'nullable|email',
            'shipping_address' => 'nullable|string',
            'is_inactive' => 'nullable|boolean',
        ]);

        $company->update($validated);

        return response()->json(['message' => 'Company updated successfully']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function destroy(Company $company)
    {
        $user = auth()->user();
        if (!($user->isSuperAdmin() || ($user->hasPermission('marketing.customers')))) {
            return response()->json(['message' => 'Unauthorized action. Only Super Admin or Marketing can delete companies.'], 403);
        }

        // Set child branches parent_id to null or handle them
        Company::where('parent_id', $company->company_id)->update(['parent_id' => null]);

        $company->delete();

        return response()->json(['message' => 'Company deleted successfully']);
    }

    /**
     * Download Excel template for importing branches.
     */
    public function downloadBranchTemplate()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Branch Import Template');

        $headers = [
            'Branch Name*',
            'Account Number',
            'Phone / Mobile',
            'Email',
            'Address',
            'Status (Active/Inactive)'
        ];

        foreach ($headers as $index => $header) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index + 1);
            $sheet->setCellValue($colLetter . '1', $header);
        }

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'C00000']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]]
        ];
        $sheet->getStyle('A1:F1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(28);



        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'Branch_Import_Template.xlsx';

        return response()->streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Import branches from uploaded Excel file for a company.
     */
    public function importBranchesExcel(Request $request, Company $company)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            $file = $request->file('excel_file');
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, true);

            if (count($rows) < 2) {
                return response()->json(['message' => 'The uploaded file is empty or only contains headers.'], 422);
            }

            // Map headers in row 1
            $headers = array_map('trim', array_map('strtolower', $rows[1]));
            $colMap = [];
            foreach ($headers as $colLetter => $headerText) {
                if (str_contains($headerText, 'branch name') || str_contains($headerText, 'company name') || str_contains($headerText, 'branch')) {
                    $colMap['branch_name'] = $colLetter;
                } elseif (str_contains($headerText, 'account')) {
                    $colMap['account_number'] = $colLetter;
                } elseif (str_contains($headerText, 'phone') || str_contains($headerText, 'mobile')) {
                    $colMap['mobile'] = $colLetter;
                } elseif (str_contains($headerText, 'email')) {
                    $colMap['main_email'] = $colLetter;
                } elseif (str_contains($headerText, 'address')) {
                    $colMap['shipping_address'] = $colLetter;
                } elseif (str_contains($headerText, 'status')) {
                    $colMap['status'] = $colLetter;
                }
            }

            if (!isset($colMap['branch_name'])) {
                $colMap['branch_name'] = 'A';
                $colMap['account_number'] = 'B';
                $colMap['mobile'] = 'C';
                $colMap['main_email'] = 'D';
                $colMap['shipping_address'] = 'E';
                $colMap['status'] = 'F';
            }

            $importedCount = 0;
            for ($i = 2; $i <= count($rows); $i++) {
                $row = $rows[$i];
                $branchName = isset($colMap['branch_name']) ? trim($row[$colMap['branch_name']] ?? '') : '';

                if (empty($branchName)) {
                    continue;
                }

                $accountNumber = isset($colMap['account_number']) ? trim($row[$colMap['account_number']] ?? '') : '';
                if (empty($accountNumber)) {
                    $accountNumber = null;
                }

                $mobile = isset($colMap['mobile']) ? trim($row[$colMap['mobile']] ?? '') : '';
                if (empty($mobile)) {
                    $mobile = $company->mobile;
                }

                $mainEmail = isset($colMap['main_email']) ? trim($row[$colMap['main_email']] ?? '') : '';
                if (empty($mainEmail)) {
                    $mainEmail = $company->main_email;
                }

                $address = isset($colMap['shipping_address']) ? trim($row[$colMap['shipping_address']] ?? '') : '';
                if (empty($address)) {
                    $address = $company->shipping_address;
                }

                $statusText = isset($colMap['status']) ? strtolower(trim($row[$colMap['status']] ?? '')) : '';
                $isInactive = (in_array($statusText, ['inactive', 'disabled', '0', 'false', 'off'])) ? true : false;

                Company::create([
                    'company_name' => $branchName,
                    'parent_id' => $company->company_id,
                    'account_number' => $accountNumber,
                    'mobile' => $mobile,
                    'main_email' => $mainEmail,
                    'shipping_address' => $address,
                    'is_inactive' => $isInactive,
                ]);

                $importedCount++;
            }

            if ($importedCount === 0) {
                return response()->json(['message' => 'No valid branch rows were found in the uploaded file.'], 422);
            }

            return response()->json([
                'message' => "Successfully imported {$importedCount} branch(es) for {$company->company_name}.",
                'imported_count' => $importedCount,
                'branches' => $company->fresh()->branches
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error importing branches: ' . $e->getMessage()], 500);
        }
    }
}
