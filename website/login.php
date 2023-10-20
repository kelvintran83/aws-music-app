<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://sdk.amazonaws.com/js/aws-sdk-2.885.0.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <title>Login Page</title>
</head>
<body>
  <h1>
    Login
  </h1>
  <form id="loginForm">
    <div>
      <form action="">
        <label for="email">Email</label>
        <input id="email" type="email" placeholder=" Email " name="email"  required>
        <label for="password">Password</label> 
        <input id="password" type="password" placeholder=" Password " name="password" required>
        <button id="Login" type="submit">
          Log In
        </button>
        
        
      </form>
      
    </div>
    <br><br>
    <button ><a href="register.php">Register</a></button>
    <div id="message"></div>
  <script>
      AWS.config.update({
        region: 'us-east-1',
        credentials: {
        accessKeyId: 'ASIAXTY4V572RUYB47LZ',
        secretAccessKey: 'gQwlJHL5GH3c33/DYHguIsuP2MHX5jeSY3DnQsgE',
        sessionToken: 'FwoGZXIvYXdzEFgaDPkoUOhreNBkD2MchiLNAWzD1H1/wUoe9rPAEFITosWtiii822mrBbTlILFW6Q14CRoyvh9eiJLwiQRKQY+nmJSUmEMBRiBMYDoDno+8vRTubhzChq5y3xXNkfE1dbx6K+LAUP7WtyFpcWO4/kjdz+HzfFEUoQnUPt5yjOlY8meaK5ySINZrQN6zkA7cfK0A3F6ZLmMp1gc3gemHrwxx+qI3tr15sYGAZ5hDbsujf9Z/TkjQDczOiNg1p8dHI/iZfvNbtE66wF8FrHdnAEDeHTUMklqWYmCz3J8Np9Uo1627oQYyLaDVLVvuVGQzrJcVSjki/mxJyIb+RTx9dVgdTuI4nQ9Gozm7/tWsn/MFMjUlFg=='
        }
      });

      var ddb = new AWS.DynamoDB({ apiVersion: '2012-08-10' });
      
      
      $('#loginForm').submit(function(event) {
        event.preventDefault();
        const email = $('#email').val();
        const password = $('#password').val();
        const params = {
          TableName: 'login',
          Key: {
            'email': { S: email }
          }
        };
        
      ddb.getItem(params, function(err, data) {
        if (err) {
          $('#message').text('Error: ' + err.message);
        } else if (data && data.Item) {
          const storedPassword = data.Item.password.S;
          const storedUsername = data.Item.user_name.S;
          if (storedPassword === password.toString()) {
            $('#message').text('Login successful!');
            
              $.ajax({
                url: 'set_session.php',
                method: 'POST',
                data: { username: storedUsername },
                success: function() {
                  window.location.assign('main.php');
                },
                error: function(xhr, status, error) {
                  $('#message').text('Error setting session: ' + error);
                }
              });
            
          } else {
            $('#message').text('Invalid username or password');
          }
        } else {
          $('#message').text('Invalid username or password');
        }
        
      });
      });
  </script>
</body>
</html>