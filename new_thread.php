<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create New Thread</title>
</head>
<body>
    <h1>Create New Thread</h1>
    <form action="create_thread.php" method="post">
        <label for="category_id">Category ID:</label>
        <input type="number" name="category_id" id="category_id" required><br><br>

        <label for="user_id">User ID:</label>
        <input type="number" name="user_id" id="user_id" required><br><br>

        <label for="title">Thread Title:</label>
        <input type="text" name="title" id="title" required><br><br>

        <input type="submit" value="Create Thread">
    </form>
</body>
</html>
