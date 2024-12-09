$.ajax({
    type: "get",
    url: "/api/host/notifications",
    headers: {
        Authorization: "Bearer " + localStorage.getItem("jwt_token"),
    },
    success: function (notis) {
        console.table(notis);
        $.map(notis, function (noti, indexOrKey) {
            if ("Notification" in window) {
                Notification.requestPermission().then((permission) => {
                    if (permission === "granted") {
                        notis.forEach((noti) => {
                            new Notification(noti.guest_name, {
                                body: `${noti.guest_name} applied for your slot.`,
                            });
                        });
                    }
                });
            }
        });
    },
});
