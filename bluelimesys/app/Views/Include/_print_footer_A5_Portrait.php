<div class="row">
            <div class="col-lg-6 print-signature" align="center">
                <div class="signature">
                    <img>
                    <?=$registered_by?>
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
<script>
    function print_printable_document(){
        var originalContents = document.body.innerHTML;
        $('a').removeAttr('href');
        var printContents = document.getElementById('printable').innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
}
</script>
</div>