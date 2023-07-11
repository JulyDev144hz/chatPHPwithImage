<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat</title>
    <script src="<?= base_url('/public/swal2.js') ?>"></script>
    <script src="<?= base_url('/public/jquery-3.7.0.min.js') ?>"></script>
    <link rel="stylesheet" href="<?= base_url('/public/styles.css') ?>?<?php echo date('l jS \of F Y h:i:s A'); ?>">
</head>

<body>
    <div class="chatbox">
        <div id="chat"></div>
        <div class="chatMessage">
            <input type="file" id="img">
            <input type="text" id="chatInput">
            <button id="chatSend"><img src="<?= base_url('/public/paperAirplane.svg') ?>" alt=""></button>
            <span class="Alert hidden" id="alertIMG"><img src="" alt="preview" id="imgPreview">Imagen seleccionada <button id="buttonCancelImg">X</button></span>
        </div>
    </div>


    <script>
        let username = 'unknown'
        const getName = () => {
            Swal.fire({
                title: 'Ingrese su nombre de usuario',
                input: 'text',
                inputAttributes: {
                    autocapitalize: 'off'
                },
                confirmButtonText: 'Ingresar',
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.value) {
                    if (result.value.length > 3) {
                        username = result.value
                        return
                    }
                }
                getName()
            })
        }
        // getName()

        var conn = new WebSocket('ws://localhost:8080');
        let file = null
        let typeFile = null
        let imgURL = null
        $("#buttonCancelImg").on('click', e => {
            file = null
            typeFile = null
            imgURL = null
            $("#alertIMG").addClass('hidden')

        })

        let imgInput = document.getElementById('img')
        imgInput.addEventListener('change', e => {
            f = e.target.files[0]
            typeFile = imgInput.value.split('.')[1]
            imgURL = URL.createObjectURL(f)
            if(typeFile == 'mp4'){

                $("#imgPreview").replaceWith(`<video autoplay muted loop alt="preview" id="imgPreview">`)
            }else{
                $("#imgPreview").replaceWith(`<img alt="preview" id="imgPreview">`)

            }
            $("#imgPreview").attr('src', imgURL)
            $("#alertIMG").removeClass('hidden')


            const reader = new FileReader();
            reader.addEventListener('load', e => {
                file = reader.result
            })
            reader.readAsDataURL(f)
        })

        const scroll = () => {
            let messages = document.getElementById('chat')

            // let shouldScroll = messages.scrollTop + messages.clientHeight === messages.scrollHeight;
            // if (!shouldScroll) {
            messages.scrollTop = messages.scrollHeight;
            // }
        }

        const sendMsg = () => {
            if (file) {
                conn.send(JSON.stringify(['newMessageWithImage', [username, file, typeFile, $("#chatInput").val()]]))
                let img = imgInput.files[0]
                $("#chat").html(`${$("#chat").html()}<div class="mensaje mensajePropio">${$("#chatInput").val() ? $("#chatInput").val()+"<br>": ""}${typeFile == 'mp4' ? `<video autoplay controls muted><source src="${imgURL}" type="video/mp4"></video>` : `<img src="${imgURL}">`}</div>`)
                scroll()
                $("#chatInput").val("")
                file = null
                typeFile = null
                imgURL = null
                $("#alertIMG").addClass('hidden')
                return
            }
            if ($("#chatInput").val().length < 1) return

            conn.send(JSON.stringify(['newMessage', [username, $("#chatInput").val()]]))

            $("#chat").html(`${$("#chat").html()}<div class="mensaje mensajePropio">${$("#chatInput").val()}</div>`)
            scroll()


            $("#chatInput").val("")
        }
        $("#chatSend").click(sendMsg)
        $("#chatInput").keypress(e => e.which == 13 ? sendMsg() : true)

        conn.onopen = function(e) {
            console.log("Connection established!");
        };
        conn.onerror = e => {
            Swal.fire({
                title: "Error",
                text: "Hubo un error con la conexion!",
                icon: "error"
            }).then(e => location.reload())
        }
        conn.onclose = e => {
            Swal.fire({
                title: "Error",
                text: "Se cerro la conexion!",
                icon: "error"
            }).then(e => location.reload())
        }

        conn.onmessage = function(e) {
            console.log(e.data);
            e = JSON.parse(e.data)
            if (e[0] == 'newMessage') {
                let data = e[1]
                $("#chat").html(`${$("#chat").html()}<div class="mensaje"><b>${data[0]}:</b> ${data[1]}<br></div>`)
                scroll()
            }
            if (e[0] == 'newMessageWithImage') {
                let data = e[1]

                $("#chat").html(`${$("#chat").html()}<div class="mensaje"><b>${data[0]}:</b>${data[2]}<br>${data[1].endsWith('mp4') ? `<video autoplay controls muted><source src="${data[1]}" type="video/mp4"></video>` : `<img src="${data[1]}">`}<br></div>`)
                scroll()

            }

        };
    </script>
</body>

</html>