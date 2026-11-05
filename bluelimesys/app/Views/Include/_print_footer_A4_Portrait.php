        <div class="row">
            <hr>
            <div class="col-lg-6 print-signature" align="center">
                <div class="signature">
                    <img>
                    <?=@$recieved_by[0]?>
                </div>
                <div class="signee">
                    <?=$footer1?>
                </div>
            </div>
            <div class="col-lg-6 print-signature" align="center">
                <div class="signature">
                    <img>
                </div>
                <div class="signee">
                <?=$footer2?>
                </div>
            </div>
            
        </div>

    </div>
    <div class="print-footer">System developed by Bluelime Technologies | bluelimeplc.com</div>
    <div>&nbsp;</div>
<script>
    function print_printable_document(){
        var printContents = document.getElementById('printable').innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
}
</script>
</div>