<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Notification</title>
</head>

<body>

    <div id="notificationId"></div>

    <div style="height: 100vh;width:100%">

    </div>
    <script type="module">
        // Import the functions you need from the SDKs you need
        import {
            initializeApp
        } from "https://www.gstatic.com/firebasejs/10.13.0/firebase-app.js";
        import {
            getMessaging,
            getToken
        } from "https://www.gstatic.com/firebasejs/10.13.0/firebase-messaging.js";



        // Your web app's Firebase configuration
        const firebaseConfig = {
            apiKey: "AIzaSyC8avcZ7IrUXdTpIRjXfAtxaBLZ6lc1TKM",
            authDomain: "femi-rides.firebaseapp.com",
            projectId: "femi-rides",
            storageBucket: "femi-rides.appspot.com",
            messagingSenderId: "356259589734",
            appId: "1:356259589734:web:3e2e780ffb2e954dec07a7"
        };

        // Initialize Firebase
        const app = initializeApp(firebaseConfig);


        // Get registration token. Initially this makes a network call, once retrieved
        // subsequent calls to getToken will return from cache.
        const messaging = getMessaging();
        getToken(messaging, {
            vapidKey: 'BE8U50HTQEqbHuxlRwP9YydgPZuRiZjKQHxmZKCKmA1WDzsAW98GV51s7n5fjiJ6wbbLHz7Tl7gIwlxHEsVvbb8'
        }).then((currentToken) => {
            if (currentToken) {
                // Send the token to your server and update the UI if necessary
                // ...
                document.getElementById('notificationId').textContent = currentToken
            } else {
                // Show permission request UI
                console.log('No registration token available. Request permission to generate one.');
                // ...
            }
        }).catch((err) => {
            console.log('An error occurred while retrieving token. ', err);
            // ...
        });

    </script>

</body>

</html>
