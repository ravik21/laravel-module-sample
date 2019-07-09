<?php namespace Modules\User\Api\Transformers;

use Modules\User\Entities\Invoice;
use League\Fractal\TransformerAbstract;

class InvoiceTransformer extends TransformerAbstract
{
    /**
     * Transform Media File.
     *
     * @param File $File
     * @return League\Fractal\ItemResource
     */
    public function transform(Invoice $invoice)
    {
        $invoiceData = json_decode($invoice->data, true);
        $transformed = [
            'id'                => $invoice->id,
            'user_id'           => $invoice->user_id,
            'stripe_invoice_id' => $invoice->stripe_invoice_id,
            'data'              => $invoice->data,
            'description'       => $invoiceData['lines']['data'][0]['description'],
            'amount'            => $invoiceData['total'],
            'status'            => $invoiceData['status'],
            'number'            => $invoiceData['number'],
            'created_at'        => $invoice->created_at->format('H:i:s d/m/Y')
        ];

        return $transformed;
    }
}
