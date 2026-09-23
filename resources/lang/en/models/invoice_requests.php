<?php

return array (
  'singular' => 'Invoice_Request',
  'plural' => 'Invoice_Requests',
  'fields' =>
  array (
    'id' => 'Id',
    'user_id' => 'User Id',
    'type' => 'Receipt/Payment',
    'quotation_id' => 'Quotation',
    'quotation_type_id' => 'Quotation Type',
    'lpoin_id' => 'Lpoin',
    'lpoout_id' => 'Lpo Out',
    'note' => 'Note',
    'amount' => 'Contract Value',
    'status' => 'Status',
    'created_at' => 'Created At',
    'updated_at' => 'Updated At',
  ),
  'invoice_history' =>
  array (
    'date' => 'Invoice Date',
    'invoice' => 'Invoice No',
    'amount' => 'Total Amount',
    'net' => 'Net',
    'vat' => 'Vat',
    'government' => 'Government Fee',
    'status' => 'Status'
  ),
  'request_history' =>
  array (
    'date' => 'Date',
    'user' => 'User',
    'note' => 'Note',
    'status' => 'Status',
  ),
  'payment_history' =>
  array (
    'date' => 'Date',
    'type' => 'Type',
    'note' => 'Note',
    'amount' => 'Amount',
    'status' => 'Status',
  ),
);
