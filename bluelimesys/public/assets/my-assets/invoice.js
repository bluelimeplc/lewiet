function calculateSum() {
    var t = 0,
        a = 0,
        e = 0,
        o = 0;
    $(".total_tax").each(function() {
        isNaN(this.value) || 0 == this.value.length || (a += parseFloat(this.value))
    }), $("#total_tax_ammount").val(a.toFixed(0)), $(".total_price").each(function() {
        isNaN(this.value) || 0 == this.value.length || (t += parseFloat(this.value))
    }), o = a.toFixed(0), e = t.toFixed(0), $("#grandTotal").val(+o + +e)
}

function invoice_paidamount() {
    var t = $("#grandTotal").val(),
        p = $("#PreviousAmount").val(),
        a = $("#paidAmount").val(),
        e = t - p - a;
    $("#dueAmmount").val(e)
}