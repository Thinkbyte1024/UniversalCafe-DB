// File untuk melakukan event menu navigasi

document.addEventListener('DOMContentLoaded', () => {

    // Mengambil seluruh element bernama "navbar-burger"
    const $navbarBurgers = Array.prototype.slice.call(document.querySelectorAll('.navbar-burger'), 0);

    // Periksa jika ada tombol "navbar burger"
    if ($navbarBurgers.length > 0) {

        // Tambahkan masing-masing event click padanya
        $navbarBurgers.forEach( el => {
            el.addEventListener('click', () => {

                // Ambil target melalui atribut "data-target"
                const target = el.dataset.target;
                const $target = document.getElementById(target);

                // Ambil tombol melalui nama kelas didalam "data-target" dan alihkan
                const btn_target = document.getElementById(target).getElementsByClassName("button")[0];
                btn_target.classList.toggle('is-fullwidth');

                // Alihkan dengan kelas "is-active" pada kelas "navbar-burger" dan "navbar-menu"
                el.classList.toggle('is-active');
                $target.classList.toggle('is-active');

            });
        });
    }
});