<!DOCTYPE html>
<html>

<head>
    <title>Media Upload Test</title>
</head>

<body>
    <h1>Test Media Upload</h1>

    <form action="/test-media-upload" method="POST" enctype="multipart/form-data">
        @csrf
        <div>
            <label>Upload Image (cover):</label>
            <input type="file" name="image">
        </div>
        <div>
            <label>Upload Video (lesson):</label>
            <input type="file" name="video">
        </div>
        <button type="submit">Upload</button>
    </form>
</body>

</html>
