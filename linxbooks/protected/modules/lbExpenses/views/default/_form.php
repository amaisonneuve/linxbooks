<?php
/* @var $this LbExpensesController */
/* @var $model LbExpenses */
/* @var $form CActiveForm */
$valuePv = false;

if(isset($_REQUEST['idPV']))
{
    $valuePv = $_REQUEST['idPV'];
}
echo '<input type="hidden" value="'.$valuePv.'"  id="idPv">';
?>

<div class="form">

<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'id'=>'lb-expenses-form',
        'enableAjaxValidation'=>true,

        'type'=>'horizontal',
        'htmlOptions' => array('enctype' => 'multipart/form-data','onsubmit'=>"validation();"), // ADD THIS
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	
     
        
        <?php echo $form->hiddenField($model,'lb_record_primary_key'); ?>
        
        <?php 
     
            $ExpensesNo = LbExpenses::model()->FormatExpensesNo(LbExpenses::model()->getExpensesNextNum());
            echo $form->textFieldRow($model, 'lb_expenses_no', array('class'=>'span3', 'value'=>$ExpensesNo));
            
            
            $category_arr = UserList::model()->getItemsForListCode('expenses_category');
            echo $form->dropDownListRow($model, 'lb_category_id', CHtml::listData($category_arr, 'system_list_item_id', 'system_list_item_name'));
         
            
            echo $form->textFieldRow($model, 'lb_expenses_date', 
                    array('hint'=>'Format: dd-mm-yyyy, e.g. 31-12-2013', 'value'=>$model->lb_expenses_date ? date('d-m-Y', strtotime($model->lb_expenses_date)) : date('d-m-Y')));
     
            echo $form->textFieldRow($model, 'lb_expenses_amount',array('class'=>'span3', 'value'=>'0.00'));
            echo '<span id="vacation"></span>';
            echo '<hr/>';
            
            $customer_arr = LbCustomer::model()->getCompanies($sort = 'lb_customer_name ASC', LbCustomer::LB_QUERY_RETURN_TYPE_DROPDOWN_ARRAY);
            $option_customer = $customer_arr;
//            echo '<div class="control-group">';
//            echo CHtml::label('Customer', 'LbExpenses_lb_customer_id',array('class'=>'control-label'));
//            echo '<div class="controls">';
//            echo CHtml::dropDownList('LbExpenses_lb_customer_id', '', $option_customer, array('multiple'=>true));
////            echo Chosen::activeMultiSelect($model,'lb_expenses_amount', $option_customer, array('class'=>'span6'));
//            echo '</div>';
//            echo '</div>';
            echo $form->dropDownListRow($customerModel, 'lb_customer_id', $option_customer, array('multiple'=>true));
            
            $status='("'.LbInvoice::LB_INVOICE_STATUS_CODE_OPEN.'","'.LbInvoice::LB_INVOICE_STATUS_CODE_OVERDUE.'")';
            $invoices = LbInvoice::model()->getInvoiceByStatus($status);
            $invoice_arr = array();
            if (count($invoices) > 0) {
                foreach ($invoices->data as $data) {
                    $invoice_arr[$data->lb_record_primary_key] = $data->lb_invoice_no;
                }
            }
            if (count($invoice_arr) <= 0)
                $option_invoice = array(''=>'Choose Invoice');
            else $option_invoice = $invoice_arr;
//            echo '<div class="control-group">';
//            echo CHtml::label('Invoices', 'LbExpenses_lb_invoice_id',array('class'=>'control-label'));
//            echo '<div class="controls">';
//            echo CHtml::dropDownList('LbExpenses_lb_invoice_id', '', $option_invoice, array('multiple'=>true));
////            echo Chosen::activeMultiSelect($model,'lb_expenses_amount', $option_customer, array('class'=>'span6'));
//            echo '</div>';
//            echo '</div>';
            echo $form->dropDownListRow($invoiceModel, 'lb_invoice_id', $option_invoice, array('multiple'=>true));
            
            echo $form->textAreaRow($model,'lb_expenses_note',array('rows'=>'3','cols'=>'40','style'=>'width:210px;'));
            
            $recurring_arr = SystemList::model()->getItemsForListCode('recurring');
            $option_recurring = array(''=>'Choose Recurring');//+$recurring_arr;
            echo $form->dropDownListRow($model, 'lb_expenses_recurring_id', $option_recurring);
            
//            $bank_accounts = LbBankAccount::model()->getBankAccount(Yii::app()->user->id);
            $bank_account_arr = array();
            if (count($bankaccounts) > 0) {
                foreach ($bankaccounts as $data_bank) {
                    $bank_account_arr[$data_bank->lb_record_primary_key] = $data_bank->lb_bank_account;
                }
            }
            $option_bank_acc = array(''=>'Choose Bank Account')+$bank_account_arr;
            echo $form->dropDownListRow($model, 'lb_expenses_bank_account_id', $option_bank_acc);
            
            $tax_arr = SystemList::model()->getItemsForListCode('taxes');
            $option_tax = array(''=>'--')+$tax_arr;
            echo '<div class="control-group">';
            echo CHtml::label('Taxes', 'LbExpenses_lb_tax_id',array('class'=>'control-label'));
            echo '<div class="controls">';
            echo CHtml::dropDownList('LbExpenses_lb_tax_id', '', $option_tax, array('style'=>'width:50px;'));echo '&nbsp;&nbsp;&nbsp;';
            echo CHtml::textField('LbExpenses_lb_expenses_tax_total', '', array('class'=>'span2'));echo '&nbsp;&nbsp;&nbsp;';
            echo CHtml::link('More tax', '#', array('onclick'=>'addTaxClick(); return false;'));
            echo '</div>';
            echo '</div>';
            
        ?>
        
        <div class="control-group">
            <?php echo CHtml::label('Attachments', 'LbExpenses_lb_expenses_document',array('class'=>'control-label required')); ?>
            <div class="controls">
                <?php
                    $this->widget('CMultiFileUpload', array(
                                    'name' => 'documents',
                                    'accept' => 'jpeg|jpg|gif|png|pdf|odt|docx|doc|dia', // useful for verifying files
                                    'duplicate' => 'Duplicate file!', // useful, i think
                                    'denied' => 'Invalid file type', // useful, i think
                                    'htmlOptions'=>array('class'=>'multi'),
                                ));
                ?>
            </div>
        </div>

	<div class="form-actions">
		<?php  echo CHtml::SubmitButton('Save',array('onclick'=>'return validation();')); ?>
            
                <?php 
                $this->widget('bootstrap.widgets.TbButton', array(
                    'label'=>'Back',
                    'url'=>LbExpenses::model()->getActionURLNormalized('admin'),
                ));
                ?>
                <?php // LBApplicationUI::backButton('admin'); ?>
	</div>
     

<?php $this->endWidget(); ?>

</div><!-- form -->

<script language="javascript">
    $(document).ready(function(){
        var from_date = $("#LbExpenses_lb_expenses_date").datepicker({
            format: 'dd-mm-yyyy'
        }).on('changeDate', function(ev) {
            from_date.hide();
        }).data('datepicker');	
    });
    
    function addTaxClick()
    {
        $.post("<?php echo $model->getActionURLNormalized('createBlankTax', array('id'=>$model->lb_record_primary_key));?>",
            function(response){
                var responseJSON = jQuery.parseJSON(response);
                if (responseJSON != null && responseJSON.success == 1)
                {
                    lbinvoice_new_tax_added = true;
                    submitTaxesAjaxForm();
                }
            });
    }
    

    function validation(){
        var lb_expenses_amount =jQuery("#LbExpenses_lb_expenses_amount").val();
        var success=true;
        var idPv = $('#idPv').val();
        
        if(lb_expenses_amount=='0.00'){
            //alert("Amount cannot be blank");
            jQuery('#vacation').html('Amount  cannot be blank.').show();
            success=false;

        }
        
        return success;
    }
</script>
