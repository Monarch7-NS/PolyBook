<?php include 'includes/header.php'; ?>

    <section class="hero">
        <div class="container">
            <h2>Welcome to Your Digital Library</h2>
            <p>Discover thousands of books at your fingertips</p>
            <form action="search.php" method="GET" class="search-form">
                <input type="text" name="query" placeholder="Search for books...">
                <button type="submit">Search</button>
            </form>
        </div>
    </section>

    <section class="featured-books">
        <div class="container">
            <h2>Featured Books</h2>
            <div class="book-grid">
                <?php
                // Connect to database
                require_once 'includes/db.php';
                
                // Get featured books
                $query = "SELECT * FROM books WHERE featured = 1 LIMIT 4";
                $result = mysqli_query($conn, $query);
                
                // Display featured books
                while($book = mysqli_fetch_assoc($result)) {
                    echo '<div class="book-card">';
                    echo '<img src="images/books/' . $book['image'] . '" alt="' . $book['title'] . '">';
                    echo '<h3>' . $book['title'] . '</h3>';
                    echo '<p class="author">by ' . $book['author'] . '</p>';
                    echo '<a href="book-details.php?id=' . $book['id'] . '" class="btn">View Details</a>';
                    echo '</div>';
                }
                ?>
            </div>
        </div>
    </section>

    <section class="categories">
        <div class="container">
            <h2>Browse by Category</h2>
            <div class="category-list">
                <?php
                // Get categories
                $query = "SELECT * FROM categories LIMIT 6";
                $result = mysqli_query($conn, $query);
                
                // Display categories
                while($category = mysqli_fetch_assoc($result)) {
                    echo '<div class="category-item">';
                    echo '<a href="category.php?id=' . $category['id'] . '">' . $category['name'] . '</a>';
                    echo '</div>';
                }
                ?>
            </div>
        </div>
    </section>

<?php include 'includes/footer.php'; ?>