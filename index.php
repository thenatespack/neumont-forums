<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Neumont Forums</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-black text-yellow-400 min-h-screen flex flex-col font-sans">

    <?php include 'navbar.php'; ?>

    <main class="container mx-auto px-6 py-10 flex-grow">
        <h1 class="text-5xl font-extrabold mb-10 text-center text-yellow-400 drop-shadow-lg">
            Welcome to Neumont Forums
        </h1>

        <?php
        require_once 'db_connect.php';

        $categories = getCategories();

        if (count($categories) === 0) {
            echo "<p class='text-center text-yellow-300 italic'>No categories available.</p>";
        } else {
            echo "<ul class='max-w-xl mx-auto space-y-4'>";
            foreach ($categories as $category) {
                $catName = htmlspecialchars($category['name']);
                $catId = (int)$category['category_id'];
                echo "<li>
                        <a href='category.php?id=$catId' class='block p-5 bg-yellow-900 bg-opacity-20 rounded-lg shadow-md hover:bg-yellow-400 hover:text-black transition font-semibold text-lg text-center'>
                            $catName
                        </a>
                      </li>";
            }
            echo "</ul>";
        }
        ?>
    </main>

    <footer class="bg-yellow-400 text-black py-4 text-center font-semibold">
        &copy; <?php echo date('Y'); ?> Neumont Forums
    </footer>

</body>

</html>
