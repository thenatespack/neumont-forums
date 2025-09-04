<?php include "navbar.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create New Thread</title>
</head>
<body class="bg-black text-yellow-400 min-h-screen flex flex-col">

    <div class="flex-grow flex items-center justify-center">
        <div class="bg-gray-900 p-8 rounded-2xl shadow-lg w-full max-w-lg">
            <h1 class="text-3xl font-bold mb-6 text-center">Create New Thread</h1>
            
            <form action="create_thread.php" method="post" class="space-y-4">
                <div>
                    <label for="category_id" class="block font-semibold mb-1">Category ID</label>
                    <input type="number" name="category_id" id="category_id" required
                        class="w-full p-2 rounded-md bg-black border border-yellow-400 focus:outline-none focus:ring-2 focus:ring-yellow-300">
                </div>

                <div>
                    <label for="user_id" class="block font-semibold mb-1">User ID</label>
                    <input type="number" name="user_id" id="user_id" required
                        class="w-full p-2 rounded-md bg-black border border-yellow-400 focus:outline-none focus:ring-2 focus:ring-yellow-300">
                </div>

                <div>
                    <label for="title" class="block font-semibold mb-1">Thread Title</label>
                    <input type="text" name="title" id="title" required
                        class="w-full p-2 rounded-md bg-black border border-yellow-400 focus:outline-none focus:ring-2 focus:ring-yellow-300">
                </div>

                <div class="flex justify-center">
                    <input type="submit" value="Create Thread"
                        class="bg-yellow-400 text-black px-6 py-2 rounded-md font-semibold hover:bg-yellow-300 transition">
                </div>
            </form>
        </div>
    </div>

</body>
</html>
