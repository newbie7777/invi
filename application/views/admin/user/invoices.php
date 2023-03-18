<div class="content-wrapper">
    <section class="content">
        <div class="container">
        	<div class="row">
            	<div class="col-md-12">
            		<h2><?php echo trans('invoices') ?> 
                    <?php if (check_payment_status() == TRUE || settings()->enable_paypal == 0 || user()->user_type == 'trial'): ?>
                        <a href="<?php echo base_url('admin/invoice/create') ?>" class="btn btn-info btn-rounded pull-right"><i class="fa fa-plus"></i> <?php echo trans('create-new-invoices') ?></a>
                    <?php endif; ?>
                 </h2>

                    <form method="GET" class="sort_invoice_form" action="<?php echo base_url('admin/invoice/type/3') ?>">
                        <div class="row p-15 mt-20 mb-20">
                            <div class="col-md-4 col-xs-12 mt-5 pl-0">
                                <select class="form-control single_select sort" name="customer">
                                    <option value=""><?php echo trans('all-customers') ?></option>
                                    <?php foreach ($customers as $customer): ?>
                                      <option value="<?php echo html_escape($customer->id) ?>" <?php echo(isset($_GET['customer']) && $_GET['customer'] == $customer->id) ? 'selected' : ''; ?>
                                      ><?php echo html_escape($customer->name) ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>

                            <div class="col-md-3 col-xs-12 mt-5 pl-0">
                                <select class="form-control single_select sort" name="status">
                                   <option value="" <?php echo(isset($_GET['status']) && $_GET['status'] == 0) ? 'selected' : ''; ?>><?php echo trans('all-status') ?></option>
                                   <option value="2" <?php echo(isset($_GET['status']) && $_GET['status'] == 2) ? 'selected' : ''; ?> ><?php echo trans('paid') ?></option>
                                   <option value="1" <?php echo(isset($_GET['status']) && $_GET['status'] == 1) ? 'selected' : ''; ?> ><?php echo trans('unpaid') ?></option>
                                   <option value="4" <?php echo(isset($_GET['status']) && $_GET['status'] == 4) ? 'selected' : ''; ?> ><?php echo trans('draft') ?></option>
                                   <option value="3" <?php echo(isset($_GET['status']) && $_GET['status'] == 3) ? 'selected' : ''; ?> ><?php echo trans('sent') ?></option>
                                </select>
                            </div>

                            <div class="col-md-2 col-xs-12 mt-5 pl-0">
                                <div class="input-group">
                                    <input type="text" class="inv-dpick form-control datepicker" placeholder="<?php echo trans('from') ?>" name="start_date" value="<?php if(isset($_GET['start_date'])){echo $_GET['start_date'];} ?>" autocomplete="off">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                </div>
                            </div>

                            <div class="col-md-2 col-xs-12 mt-5 pl-0">
                                <div class="input-group">
                                    <input type="text" class="inv-dpick form-control datepicker" placeholder="<?php echo trans('to') ?>" name="end_date" value="<?php if(isset($_GET['end_date'])){echo $_GET['end_date'];} ?>" autocomplete="off">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                </div>
                            </div>
                        
                            <div class="col-md-1 col-xs-12 mt-5 pl-0">
                                <button type="submit" class="btn btn-info btn-report btn-block custom_search"><i class="flaticon-magnifying-glass"></i></button>
                            </div>
                        </div>
                    </form>
            		
            		<ul class="nav nav-tabs custab" role="tablist">
                        <li class="nav-item"> <a class="nav-link <?php if($status == 1){echo "active";} ?>" href="<?php echo base_url('admin/invoice/type/1') ?>" role="tab" aria-selected="true"> <span class="hidden-xs-downs"><?php echo trans('unpaid') ?> <span class="label-count bg-danger"><?php echo helper_count_invoices($istatus=1, $type=1) ?></span></span></a> </li>
                        <li class="nav-item"> <a class="nav-link <?php if($status == 0){echo "active";} ?>" href="<?php echo base_url('admin/invoice/type/0') ?>" role="tab" aria-selected="false"> <span class="hidden-xs-downs"><?php echo trans('draft') ?> <span class="label-count bg-secondary"><?php echo helper_count_invoices($istatus=0, $type=1) ?></span></span></a> </li>
                        <li class="nav-item"> <a class="nav-link <?php if($status == 3 && empty($_GET['recurring'])){echo "active";} ?>" href="<?php echo base_url('admin/invoice/type/3') ?>" role="tab" aria-selected="false"> <span class="hidden-xs-downs"><?php echo trans('all-invoices') ?> <span class="label-count"><?php echo helper_count_invoices($istatus=3, $type=1) ?></span></span></a> </li>

                        <li class="nav-item"> <a class="nav-link <?php if(isset($_GET['recurring']) && $_GET['recurring'] == 1){echo "active";} ?>" href="<?php echo base_url('admin/invoice/type/3?recurring=1') ?>" role="tab" aria-selected="false"> <span class="hidden-xs-downs"><?php echo trans('recurring-invoice') ?> <span class="label-count bg-success"><?php echo count_recurring_invoices() ?></span></span></a> </li>
                    </ul>


                    <div class="tab-content">
                        <!-- All -->
                        <div class="tab-pane active" id="messages2" role="tabpanel">

                            <?php if (!empty($invoices)): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover cushover">
                                        <thead>
                                            <tr class="item-row">
                                                <th><?php echo trans('status') ?></th>
                                                <th><?php echo trans('date') ?></th>
                                                <th><?php echo trans('number') ?></th>
                                                <th><?php echo trans('customer') ?></th>
                                                <th><?php echo trans('total') ?></th>
                                                <th><?php echo trans('amount-due') ?></th>
                                                <th class="text-right"><?php echo trans('actions') ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            <?php foreach ($invoices as $invoice): ?>
                                                <tr id="row_<?php echo html_escape($invoice->id) ?>">
                                                    <td>
                                                        <?php if ($invoice->status == 0): ?>
                                                            <span data-toggle="tooltip" data-placement="right" title="<?php echo trans('draft-tooltip') ?>" class="custom-label-sm label-light-default"><?php echo trans('draft') ?></span>
                                                        <?php elseif($invoice->status == 2): ?>
                                                            <?php if ($invoice->type == 4): ?>
                                                                <span data-toggle="tooltip" data-placement="right" title="" class="custom-label-sm label-light-warning" style="width: 90px"><?php echo trans('credit-note') ?></span>
                                                            <?php else: ?>
                                                                <span data-toggle="tooltip" data-placement="right" title="<?php echo trans('paid-tooltip') ?>" class="custom-label-sm label-light-success"><?php echo trans('paid') ?></span>
                                                            <?php endif ?>
                                                        <?php elseif($invoice->status == 1): ?>
                                                            <?php if (check_paid_status($invoice->id) == 1): ?>
                                                                <span data-toggle="tooltip" data-placement="right" title="<?php echo trans('partial-payment') ?>" class="custom-label-sm label-light-info"><?php echo trans('partial') ?></span>
                                                            <?php else: ?>
                                                                <span data-toggle="tooltip" data-placement="right" title="<?php echo trans('unpaid-tooltip') ?>" class="custom-label-sm label-light-danger"><?php echo trans('unpaid') ?></span>
                                                            <?php endif ?>
                                                            
                                                        <?php endif ?>

                                                        <?php if ($invoice->recurring == 1): ?>
                                                            <?php if ($invoice->is_completed == 0): ?>
                                                                <span class="custom-label-sm label-light-success mt-5"><?php echo trans('active') ?></span>
                                                            <?php elseif($invoice->is_completed == 1): ?>
                                                                <span data-toggle="tooltip" data-placement="right" title="<?php echo trans('complete-tooltip') ?>" class="custom-label-sm label-light-danger mt-5"><?php echo trans('completed') ?></span>
                                                            <?php endif ?>
                                                        <?php endif ?>
                                                    </td>
                                                    
                                                    <td><?php echo my_date_show($invoice->date); ?></td>
                                                    <td>
                                                        <p class="mb-0"> <?php echo html_escape($invoice->number) ?> </p>
                                                        <?php if ($invoice->recurring == 1): ?>
                                                            <strong><?php echo trans('recurring') ?></strong>
                                                        <?php endif ?>
                                                    </td>
                                                    <td>
                                                        <?php if (!empty(helper_get_customer($invoice->customer))): ?>
                                                            <?php echo helper_get_customer($invoice->customer)->name ?>
                                                            <?php 
                                                                $currency_symbol = helper_get_customer($invoice->customer)->currency_symbol;
                                                                if (isset($currency_symbol)) {
                                                                    $currency_symbol = $currency_symbol;
                                                                } else {
                                                                    $currency_symbol = $this->business->currency_symbol;
                                                                }
                                                            ?>
                                                            <?php $currency_code = helper_get_customer($invoice->customer)->currency_code ?>
                                                        <?php endif ?>
                                                    </td>

                                                    <?php if($invoice->status == 2): ?>
                                                        <td>
                                                            <span class="total-price"> <?php echo price_formatted_alt($invoice->grand_total, $this->business->id, $currency_symbol) ?> </span><br>
                                                            <span class="conver-total"><?php echo price_formatted($invoice->convert_total, $this->business->id) ?></span>
                                                        </td>
                                                        <td>
                                                            <span class="total-price"><?php echo price_formatted_alt(0, $this->business->id, $currency_symbol) ?> </span>
                                                            <br>
                                                            <span class="conver-total"><?php echo price_formatted('0', $this->business->id) ?></span>
                                                        </td>
                                                    <?php else: ?>
                                                        <td>
                                                            <span class="total-price"><?php echo price_formatted_alt($invoice->grand_total, $this->business->id, $currency_symbol) ?> </span><br>
                                                            <span class="conver-total"><?php echo price_formatted($invoice->convert_total, $this->business->id) ?></span>
                                                        </td>
                                                        
                                                        <td class="text-danger">
                                                            <span class="total-price">
                                                                <?php $due_total = $invoice->grand_total - get_total_invoice_payments($invoice->id, 0); ?>
                                                                <?php echo price_formatted_alt($due_total, $this->business->id, $currency_symbol) ?> 
                                                            </span><br>
                                                            <?php if ($invoice->status != 1): ?>
                                                                
                                                                <span class="conver-total"><?php echo price_formatted($invoice->convert_total, $this->business->id) ?></span>
                                                            <?php endif ?>
                                                        </td>
                                                    <?php endif ?>

                                                    <td class="text-right">
                                                        <?php if($invoice->status == 2): ?>
                                                            <?php if($invoice->type != 4): ?>
                                                                <a target="_blank" class="mr-5 hide_viewer" href="<?php echo base_url('admin/invoice/details/'.md5($invoice->id)) ?>"><?php echo trans('view') ?></a>
                                                            <?php endif; ?>
                                                        <?php elseif($invoice->status == 0): ?>
                                                            <a class="mr-5 approve_item hide_viewer" href="<?php echo base_url('admin/invoice/approve_invoice/'.md5($invoice->id)) ?>"><?php echo trans('approve') ?></a>
                                                        <?php else: ?>
                                                            <a class="mr-5 hide_viewer record_payment" data-id="<?php echo md5($invoice->id); ?>" href="#" data-toggle="modal"><?php echo trans('record-a-payment') ?></a>
                                                        <?php endif ?>

                                                        <div class="btn-group">
                                                            <button type="button" class="btn btn-default rounded btn-sm dropdown-toggle d-block" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                               <?php echo trans('more') ?>
                                                            </button>
                                                            <div class="dropdown-menu st" x-placement="bottom-start">
                                                                
                                                                <?php if(auth('role') != 'viewer'): ?>

                                                                <?php if($invoice->status != 2): ?>
                                                                <a class="dropdown-item" href="<?php echo base_url('admin/invoice/details/'.md5($invoice->id)) ?>"><?php echo trans('view') ?>
                                                                </a>
                                                                <?php endif ?>
                                                                <a class="dropdown-item" data-toggle="modal" href="#sendInvoiceModal_<?php echo html_escape($invoice->id) ?>"><?php echo trans('send') ?></a>
                                                                <a class="dropdown-item" href="<?php echo base_url('admin/invoice/details/'.md5($invoice->id)) ?>"><?php echo trans('print') ?></a>


                                                                <?php if ($invoice->type == 4): ?>
                                                                    <a class="dropdown-item revert_to_invoice" href="<?php echo base_url('admin/invoice/revert_credit_note/'.md5($invoice->id)) ?>"><?php echo trans('revert-invoice') ?></a>
                                                                <?php endif ?>

                                                                <?php if ($invoice->recurring == 1 && $invoice->is_completed == 0): ?>
                                                                <a class="dropdown-item stop_recurring" href="<?php echo base_url('admin/invoice/stop_recurring/'.$invoice->id) ?>"><?php echo trans('stop-recurring') ?></a>
                                                                <?php endif ?>

                                                                <?php if ($invoice->recurring == 0): ?>
                                                                    <a class="dropdown-item convert_to_recurring" href="<?php echo base_url('admin/invoice/convert_recurring/'.md5($invoice->id)) ?>"><?php echo trans('convert-recurring') ?></a>
                                                                <?php endif ?>

                                                                <?php if (settings()->pdf_type == 1): ?>
                                                                    <a href="#" data-id="invoice_<?php echo rand() ?>" class="dropdown-item btnExport"><?php echo trans('download-pdf') ?> </a>
                                                                <?php else: ?> 
                                                                    <a target="_blank" class="dropdown-item" href="<?php echo base_url('readonly/export_pdf/'.md5($invoice->id)) ?>"><?php echo trans('export-as-pdf') ?></a>
                                                                <?php endif ?>

                                                                <!-- <a class="dropdown-item" href="<?php //echo base_url('readonly/export_pdf/'.md5($invoice->id)) ?>"><?php //echo trans('export-as-pdf') ?></a> -->
                                                                
                                                                <a target="_blank" class="dropdown-item" href="<?php echo base_url('readonly/invoice/preview/'.md5($invoice->id)) ?>"><?php echo trans('preview-as-a-customer') ?></a>

                                                                <a target="_blank" class="dropdown-item" href="<?php echo base_url('readonly/invoice/view/'.md5($invoice->id)) ?>"><?php echo trans('share-link') ?></a>

                                                                <?php if($this->business->enable_qrcode == 1 && $invoice->qr_code == ''): ?>
                                                                    <a class="dropdown-item" href="<?php echo base_url('admin/business/generate_qucode/'.md5($invoice->id)) ?>"><?php echo trans('generate-qr-code') ?> </a>
                                                                <?php endif; ?>
                                                                
                                                                <div class="dropdown-divider"></div>

                                                                <?php if($invoice->type != 4): ?>
                                                                    <a class="dropdown-item" href="<?php echo base_url('admin/invoice/edit/'.md5($invoice->id)) ?>"><?php echo trans('edit') ?> </a>
                                                                <?php endif; ?>
                                                                
                                                                <?php if ($settings->enable_delete_invoice == 1): ?>
                                                                    <a class="dropdown-item delete_item" data-id="<?php echo html_escape($invoice->id); ?>" href="<?php echo base_url('admin/invoice/delete/'.$invoice->id) ?>"><?php echo trans('delete') ?></a>
                                                                <?php endif ?>

                                                                <?php else: ?>
                                                                    <a class="dropdown-item" href="<?php echo base_url('admin/invoice/details/'.md5($invoice->id)) ?>"><?php echo trans('export-as-pdf') ?></a>
                                                                
                                                                    <a target="_blank" class="dropdown-item" href="<?php echo base_url('readonly/invoice/preview/'.md5($invoice->id)) ?>"><?php echo trans('preview-as-a-customer') ?></a>
                                                                <?php endif ?>

                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach ?>

                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="pt-30"><strong><?php echo trans('no-data-founds') ?></strong></div>
                            <?php endif ?>
                        </div>

                    </div>
                </div>

                <div class="col-md-12 text-center mt-50">
                    <?php echo $this->pagination->create_links(); ?>
                </div>
            </div>
        </div>
    </section>
</div>


<?php foreach ($invoices as $invoice): ?>
    <?php include"include/send_invoice_modal.php"; ?>
<?php endforeach; ?>




<!-- Model Start -->
<div id="recordPaymentModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-dialog-zoom modal-md">
    <div class="modal-content modal-md" id="load_modal">
    

    </div>
  </div> 
</div>
<!-- Modal End-->


