var App = function () {

    var handleiCheck =  function() {
        $('input.icheck').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue',
            increaseArea: '20%' // optional
        });
    };

    return {

        init: function() {
            handleiCheck();
        }
    };

}();