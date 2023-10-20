<?php
session_start();
$username = $_SESSION["username"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://sdk.amazonaws.com/js/aws-sdk-2.956.0.min.js"></script>
  <title>Main</title>
</head>
<body>
  <h1>Welcome <?php echo $username; ?></h1>
  
  <h1>Subscription Area</h1>
  <button onclick="logout()">Logout</button>
  <div id="subscription-div">
    <table>
      <thead>
        <tr>
          <th>Artist</th>
          <th>Title</th>
          <th>Year</th>      
          <th>Album Photo</th>
          <th>Actions</th>

        </tr>
      </thead>
      <tbody id="subscription-table-body">
        <!-- Songs will be added dynamically here -->
      </tbody>
    </table>
    
  </div>
  <h1>Query Area</h1>
  <div id="query-div">
    <form id="queryForm" action="">
      <label for="title">Title</label>
      <input id="title" type="text" placeholder=" Title " name="title" >
      <label for="year">Year</label>
      <input id="year" type="text" placeholder=" Year " name="year" >
      <label for="artist">Artist</label>
      <input id="artist" type="text" placeholder=" Artist " name="artist" >
      <button type="submit">Query</button>
    </form>
    <table>
      <thead>
        <tr>
          <th>Artist</th>
          <th>Title</th>
          <th>Year</th>      
          <th>Album Photo</th>
          <th>Actions</th>

        </tr>
      </thead>
      <tbody id="query-table-body">
        <!-- Songs will be added dynamically here -->
      </tbody>
    </table>
    <h3 id="query-message"></h3>
  </div>
</body>
<script>
    AWS.config.update({
    region: 'us-east-1',
        credentials: {
        accessKeyId: 'ASIAXTY4V572RUYB47LZ',
        secretAccessKey: 'gQwlJHL5GH3c33/DYHguIsuP2MHX5jeSY3DnQsgE',
        sessionToken: 'FwoGZXIvYXdzEFgaDPkoUOhreNBkD2MchiLNAWzD1H1/wUoe9rPAEFITosWtiii822mrBbTlILFW6Q14CRoyvh9eiJLwiQRKQY+nmJSUmEMBRiBMYDoDno+8vRTubhzChq5y3xXNkfE1dbx6K+LAUP7WtyFpcWO4/kjdz+HzfFEUoQnUPt5yjOlY8meaK5ySINZrQN6zkA7cfK0A3F6ZLmMp1gc3gemHrwxx+qI3tr15sYGAZ5hDbsujf9Z/TkjQDczOiNg1p8dHI/iZfvNbtE66wF8FrHdnAEDeHTUMklqWYmCz3J8Np9Uo1627oQYyLaDVLVvuVGQzrJcVSjki/mxJyIb+RTx9dVgdTuI4nQ9Gozm7/tWsn/MFMjUlFg=='
        }
  });

    const user_name = '<?php echo $username; ?>'; 
    var ddb = new AWS.DynamoDB({ apiVersion: '2012-08-10' });

    var s3 = new AWS.S3({
      apiVersion: '2006-03-01',
      params: {Bucket: 's3781137-cc-songimages'}
    });
    function renderSubscriptions(){
      const params = {
        TableName: 'Subscriptions',
        KeyConditionExpression: 'user_name = :user_name',
        ExpressionAttributeValues: {
          ':user_name': { S: user_name },
        },
      };
      ddb.query(params, (err, data) => {
        if (err) {
          console.error('Error querying user subscriptions:', err);
          return;
        }

      
      const subscriptionTableBody = document.getElementById('subscription-table-body');
      data.Items.forEach((item) => {
      const artist = item.artist.S;
      const title = item.title.S;
      const year = item.year.S;
      const row = document.createElement('tr');
      const imageKey = title + '.jpg';
      const imageCell = document.createElement('td');
      const image = document.createElement('img');
      const params = {
        Bucket: 's3781137-cc-songimages',
        Key: imageKey
      };
      s3.getObject(params, (err, data) => {
        if (err) {
          console.error('Error retrieving image from S3:', err);
          return;
        }
        const url = URL.createObjectURL(new Blob([data.Body]));
        image.src = url;
      });


      const artistCell = document.createElement('td');
      artistCell.textContent = artist;
      row.appendChild(artistCell);
      const titleCell = document.createElement('td');
      titleCell.textContent = title;
      row.appendChild(titleCell);
      const yearCell = document.createElement('td');
      yearCell.textContent = year;
      row.appendChild(yearCell);
      imageCell.appendChild(image);
      row.appendChild(imageCell);
      const actionsCell = document.createElement('td');
      const removeButton = document.createElement('button');
      removeButton.textContent = 'Remove';
      removeButton.onclick = () => removeSong(item.web_url.S);
      actionsCell.appendChild(removeButton);
      row.appendChild(actionsCell);
      row.id=item.web_url.S
      

      subscriptionTableBody.appendChild(row);
    });
  });
    }
    renderSubscriptions();


    function removeSong(web_url) {
      const params = {
        TableName: 'Subscriptions',
        Key: {
          user_name: { S: user_name },
          web_url: { S: web_url }
        }
      };
      ddb.deleteItem(params, (err, data) => {
        if (err) {
          console.error('Error deleting song from subscriptions:', err);
          return;
        }
        const row = document.getElementById(web_url);
        row.remove();
      });
    }

$('#queryForm').submit(function(event) {
    event.preventDefault();
    const title = $('#title').val();
    const artist = $('#artist').val();
    const year = $('#year').val();

    let params = {
        TableName: 'Music',
        ProjectionExpression: 'web_url, img_url, artist, title, #y',
        ExpressionAttributeNames: {
            '#y': 'year'
        }
    };

    if (title && artist && year) {
        params.ExpressionAttributeValues = {
            ':t': {S: title},
            ':a': {S: artist},
            ':y': {S: year}
        };
        params.FilterExpression = 'contains(artist, :a) AND begins_with(title, :t) AND contains(#y, :y)';
    } else if (title && artist) {
        params.ExpressionAttributeValues = {
            ':t': {S: title},
            ':a': {S: artist}
        };
        params.FilterExpression = 'begins_with(artist, :a) and begins_with(title, :t)';
    } else if (artist && year) {
        params.ExpressionAttributeValues = {
            ':a': {S: artist},
            ':y': {S: year}
        };
        params.FilterExpression = 'contains(artist, :a) AND contains(#y, :y)';
    } else if (title && year) {
        params.ExpressionAttributeValues = {
            ':t': {S: title},
            ':y': {S: year}
        };
        params.FilterExpression = 'begins_with(title, :t) AND contains(#y, :y)';
    } else if (title) {
        params.ExpressionAttributeValues = {
            ':t': {S: title}
        };
        params.FilterExpression = 'begins_with(title, :t)';
    } else if (artist) {
        params.ExpressionAttributeValues = {
            ':a': {S: artist}
        };
        params.FilterExpression = 'contains(artist, :a)';
    } else if (year) {
        params.ExpressionAttributeValues = {
            ':y': {S: year}
        };
        params.FilterExpression = 'contains(#y, :y)';
    } else {
        console.log('Error: at least one search field must be filled in');
        document.getElementById('query-message').textContent = "Error: at least one search field must be filled"
        return;
    }

    ddb.scan(params, function(err, data) {
      if (err) {
        console.log("Error", err);
      } else {
        if (!data || data.Items.length === 0){
          document.getElementById('query-message').textContent = "Error: No result is retrieved. Please query again."
      } else {
        document.getElementById('query-message').textContent = ""
        const queryTableBody = document.getElementById('query-table-body');
        data.Items.forEach(function(element, index, array) {
          const artist = element.artist.S;
          const title = element.title.S;
          const year = element.year.S;
          const imageKey = title + '.jpg';
          
          const row = document.createElement('tr');
          
          const artistCell = document.createElement('td');
          artistCell.textContent = artist;
          row.appendChild(artistCell);
          
          const titleCell = document.createElement('td');
          titleCell.textContent = title;
          row.appendChild(titleCell);
          
          const yearCell = document.createElement('td');
          yearCell.textContent = year;
          row.appendChild(yearCell);
          
          const imageCell = document.createElement('td');
          const image = document.createElement('img');
          const params = {
            Bucket: 's3781137-cc-songimages',
            Key: imageKey
          };
          s3.getObject(params, (err, data) => {
            if (err) {
              console.error('Error retrieving image from S3:', err);
              return;
            }
            const url = URL.createObjectURL(new Blob([data.Body]));
            image.src = url;
          });
          imageCell.appendChild(image);
          row.appendChild(imageCell);
          
          const actionsCell = document.createElement('td');
          const subscribeButton = document.createElement('button');
          subscribeButton.textContent = 'Subcribe';
          subscribeButton.onclick = () => subscribeSong(artist, title);
          actionsCell.appendChild(subscribeButton);
          row.appendChild(actionsCell);
          
          row.id=element.web_url.S
          
          queryTableBody.appendChild(row);
        });
      }
    }
    });
    });
    function subscribeSong(artist, songTitle){
      var params ={
        Key: {
          "artist": {
            S: artist
          },
          "title": {
            S: songTitle
          }
        },
        TableName: "Music"
    };
    ddb.getItem(params, function(err, data) {
      if (err) {
        console.log("Error", err);
      } else {
        console.log("Success", data.Item);
        var params = {
          TableName: 'Subscriptions',
          Item: {
            "user_name" : {S: user_name},
            "web_url" : {S: data.Item.web_url.S},
            "artist" : {S: data.Item.artist.S},
            "img_url" : {S: data.Item.img_url.S},
            "title" : {S: data.Item.title.S},
            "year" : {S: data.Item.year.S}
          }
        };
        ddb.putItem(params, function(err, data){
          if (err) {
            console.log("Error", err);
          } else {
            console.log("Success", data);
            location.reload();

          }
        });
      }
    });

  }
  function logout(){
    $.ajax({
        url: 'delete_session.php',
        method: 'POST',
        success: function() {
          window.location.assign('login.php');
        },
        error: function(xhr, status, error) {
          $('#message').text('Error setting session: ' + error);
        }
      });
  }
    
</script>
</html>