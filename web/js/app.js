var App = function () {

    return {

        handleTaskLock: function() {
            $('.btn-lock-task').on('click', function(e){
                if (!confirm('Are you sure you want to lock this task ?')) {
                    e.preventDefault();
                }
            })
        },

        init: function() {
            this.handleTaskLock();
        }

    };

}();