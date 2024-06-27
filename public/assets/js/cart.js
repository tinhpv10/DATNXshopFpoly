document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.increase-btn').forEach(button => {
        button.addEventListener('click', function () {
            const input = this.closest('.input-group').querySelector('.quantity-input');
            input.value = parseInt(input.value) + 1;
        });
    });

    document.querySelectorAll('.decrease-btn').forEach(button => {
        button.addEventListener('click', function () {
            const input = this.closest('.input-group').querySelector('.quantity-input');
            if (input.value > 1) {
                input.value = parseInt(input.value) - 1;
            }
        });
    });
});
