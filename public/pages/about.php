<?php
require __DIR__ . '/../includes/init.php';
$pageTitle = 'About Us | ' . $siteName;
require __DIR__ . '/../includes/header.php';
?>

    <section class="content-panel about-panel">
        <div class="about-header">
            <p class="eyebrow">Our Story</p>
            <h1>About Inkforge Tees</h1>
            <p class="about-intro">
                Inkforge Tees was created at Saunders College of Business at RIT by four students who wanted to build something that reflected student creativity, school pride, and entrepreneurial thinking. What began as a website project turned into a brand centered around campus inspired apparel and a clean online shopping experience.
            </p>
        </div>

        <div class="about-history">
            <h2>How It Started</h2>
            <p>
                The idea for Inkforge Tees came together in Saunders during a group project where we were asked to create a functional ecommerce website. Instead of building a generic store, we wanted to create a brand that felt connected to RIT students and the kinds of designs we would actually want to wear.
            </p>
            <p>
                Our catalog reflects that idea through shirts inspired by RIT pride, Saunders, business, tech, coding, data, and student ambition. Every part of the site was built by our team, from the storefront and navigation to the product pages and ordering flow. Inkforge Tees represents both our technical work and our shared vision for a student built apparel brand.
            </p>
        </div>

        <div class="team-section">
            <h2>Meet the Team</h2>

            <div class="team-grid">
                <div class="team-card">
                    <h3>Prashan Adhikari</h3>
                    <p>
                        Prashan helped lead the overall structure of the project and focused on making sure the website worked smoothly across pages while keeping the brand organized and consistent.
                    </p>
                </div>

                <div class="team-card">
                    <h3>Marc Madison</h3>
                    <p>
                        Marc contributed to the design and presentation of the storefront, helping shape a simple and user friendly experience that fits the style of the brand.
                    </p>
                </div>

                <div class="team-card">
                    <h3>Mandip Rai</h3>
                    <p>
                        Mandip worked on the technical side of the website and supported the functionality behind the store, helping connect product information and user interaction throughout the site.
                    </p>
                </div>

                <div class="team-card">
                    <h3>Wes Hunt</h3>
                    <p>
                        Wes focused on testing, integration, and making sure different parts of the project came together in a way that made the website feel complete and reliable.
                    </p>
                </div>
            </div>
        </div>
    </section>

<?php require __DIR__ . '/../includes/footer.php'; ?>