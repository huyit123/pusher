<!DOCTYPE html>
<html>

<head>
    <title>Notification Example</title>
</head>

<body>
    <button id="showNotification">Hiển thị thông báo</button>

    <script>
        document.getElementById('showNotification').addEventListener('click', function() {
            if ('Notification' in window) {
                Notification.requestPermission().then(function(permission) {
                    if (permission === 'granted') {
                        var notification = new Notification('Thông báo từ trang web', {
                            body: 'Chào mừng bạn đến với ví dụ thông báo trên Chrome Android!',
                            icon: 'icon.png' // Đường dẫn đến biểu tượng tùy chọn
                        });

                        notification.onclick = function() {
                            console.log('Thông báo đã được bấm.');
                        };
                    } else {

                        alert("Quyền hiển thị thông báo đã bị từ chối.");
                    }
                });
            } else {
                alert('Trình duyệt không hỗ trợ API thông báo.');
            }
        });
    </script>
</body>

</html>
