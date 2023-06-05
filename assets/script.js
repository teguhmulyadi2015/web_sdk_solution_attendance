let loading = document.querySelector('.container')

function hide_loading() {
    let fadeOut = setInterval(function () {

        if (!loading.style.opacity) {

            loading.style.opacity = 1
        }

        if (loading.style.opacity > 0) {

            loading.style.opacity -= 0.1
        }

        else {

            clearInterval(fadeOut)
            loading.style.display = "none"

        }

    }, 0)
}