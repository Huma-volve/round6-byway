<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>

<body>

    <form action="{{ route('upload') }}" method="post" enctype="multipart/form-data">
        @csrf
        <input type="file" name="file" required>
        <button type="submit">Upload</button>
    </form>

    @if (session('uploaded_url'))
        <h3>Uploaded File:</h3>

        <img src="{{ session('uploaded_url') }}" alt="Uploaded" width="300">

        <p><a href="{{ session('uploaded_url') }}" target="_blank">View on Cloudinary</a></p>
    @endif

</body>

</html>
