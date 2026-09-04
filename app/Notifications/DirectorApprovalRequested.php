<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DirectorApprovalRequested extends Notification
{
    use Queueable;

    protected $request;
    protected $type;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($request, $type)
    {
        $this->request = $request;
        $this->type = $type;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $reference = $this->getReferenceNumber();
        $requestor = $this->getRequestorName();

        return (new MailMessage)
                    ->subject("Approval Required: {$this->type} ({$reference})")
                    ->greeting("Hello Director,")
                    ->line("A new {$this->type} request has been submitted and requires your final approval.")
                    ->line("Reference No: **{$reference}**")
                    ->line("Requestor: **{$requestor}**")
                    ->action('Review Approval Queue', url('/admin-finance/approval-queue'))
                    ->line('Please log in to the Clarentian ERP system to review and approve this request.')
                    ->line('Thank you!');
    }

    protected function getReferenceNumber()
    {
        if ($this->type === 'CCTV') return 'CCTV-' . str_pad($this->request->cctv_req_id, 4, '0', STR_PAD_LEFT);
        if ($this->type === 'Material') return 'MAT-' . str_pad($this->request->material_req_id, 4, '0', STR_PAD_LEFT);
        if ($this->type === 'QB Change') return 'QB-' . str_pad($this->request->qb_req_id, 4, '0', STR_PAD_LEFT);
        if ($this->type === 'Service') return 'SRV-' . str_pad($this->request->service_req_id, 4, '0', STR_PAD_LEFT);
        if ($this->type === 'Undertime') return 'UND-' . str_pad($this->request->undertime_req_id, 4, '0', STR_PAD_LEFT);
        if ($this->type === 'Cash Advance') return 'CA-' . str_pad($this->request->id, 4, '0', STR_PAD_LEFT);
        if ($this->type === 'Sales Order') return $this->request->so_number;
        return 'N/A';
    }

    protected function getRequestorName()
    {
        return $this->request->requested_by ?? 
               $this->request->employee_name ?? 
               $this->request->user->name ?? 
               'Unknown';
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'request_id' => $this->request->id ?? $this->request->cctv_req_id ?? $this->request->material_req_id ?? $this->request->qb_req_id ?? $this->request->service_req_id ?? $this->request->undertime_req_id,
            'type' => $this->type,
        ];
    }
}
