<!-- resources/views/pusher.blade.php -->

<!DOCTYPE html>
<html>

<head>
    <title>Pusher Demo</title>
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <h1>Pusher Demo</h1>
    <button id="send-notification">Send Notification</button>

    <script>
        const pusher = new Pusher('{{ env('PUSHER_APP_KEY') }}', {
            cluster: '{{ env('PUSHER_APP_CLUSTER') }}'
        });

        const channel = pusher.subscribe('my-channel');
        channel.bind('my-event', function(data) {
            // Kiểm tra xem trình duyệt có hỗ trợ thông báo hay không
            if ("Notification" in window) {
                // Yêu cầu sự cho phép hiển thị thông báo
                Notification.requestPermission().then(function(permission) {
                    if (permission === "granted") {
                        // Tạo và hiển thị thông báo
                        var notification = new Notification("Thông báo", {
                            body: 'Received message: ' + data.message,
                            icon: 'https://ruoungon.vn/themes/default/assets/images/logo.png' // Biểu tượng tùy chọn
                        });
                    }
                });
            } else {
                // Trình duyệt không hỗ trợ thông báo
                alert("Trình duyệt không hỗ trợ thông báo.");
            }
        });

        $('#send-notification').click(function() {
            $.post('/send-notification', {
                message: 'Hello, world!'
            });
        });
        // Kiểm tra xem trình duyệt có hỗ trợ thông báo hay không
        if ("Notification" in window) {

            if (Notification.permission === "default") {
                // Trình duyệt chưa yêu cầu quyền hiển thị thông báo, yêu cầu từ người dùng.
                Notification.requestPermission().then(function(permission) {

                    // if (permission === "granted") {
                    //     new Notification("Thông báo", {
                    //         body: 'Received message: hhhhhhh',
                    //         icon: 'https://ruoungon.vn/themes/default/assets/images/logo.png' // Biểu tượng tùy chọn
                    //     });
                    // }
                });
            } else if (Notification.permission === "granted") {
                // alert(1);
                // var abc = new Notification("Thông báo", {
                //     body: 'Received message: hhhhhhh',
                //     icon: 'https://ruoungon.vn/themes/default/assets/images/logo.png' // Biểu tượng tùy chọn
                // });

            } else if (Notification.permission === "denied") {
                // Quyền hiển thị thông báo đã bị từ chối.
            }
        } else {
            // alert('không');
        }
    </script>

</body>

</html>
