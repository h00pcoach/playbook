<div id="payments-modal" class="modal fade" data-backdrop="static" data-keyboard="false" role="dialog" tabindex="-1">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<script>
    $('#pay-save-bottom').on('click', function()
    {
        //console.log('Pay save bottom clicked!');

        //validate fields
        var fail = false;
        var fail_log = '';
        $('#payment-form').find('select, textarea, input').each(function()
        {
            if($(this).prop('required'))
            {
                if (!$(this).val())
                {
                    fail = true;
                    name = $(this).attr( 'name' );
                    fail_log += name + " is required \n";

                    // var form
                    $(this).closest('.form-group').removeClass('has-success');
                    $(this).closest('.form-group').addClass('has-error');

                    //console.log('fail_log? ', fail_log);

                } else {

                    // var form
                    $(this).closest('.form-group').removeClass('has-error');
                    $(this).closest('.form-group').addClass('has-success');
                }
            }
        });

        //submit if fail never got set to true
        if (!fail)
        {
            //process form here.
            // //console.log('Can submit!');
            $('#payment-form').submit();
        }
    });
</script>
