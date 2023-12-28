<?php
/**
 * Plugin Name: Historical Events
 * Description: Display historical events "On This Day" from Wikimedia API.
 * Version: 1.0
 * Author: Your Name
 */

function historical_events_enqueue_script() { ?>
    <script>
        function fetchEvents() {
            var xhr = new XMLHttpRequest();
            var today = new Date();
            var month = today.getMonth() + 1;
            var day = today.getDate();

            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        var response = JSON.parse(xhr.responseText);
                        displayEvents(response);
                    } else {
                        console.error('Error in API request', xhr.responseText);
                    }
                }
            };

            xhr.open("GET", "https://api.wikimedia.org/feed/v1/wikipedia/en/onthisday/selected/" + month + "/" + day, true);
            xhr.send();
        }

        function displayEvents(data) {
            var eventsList = document.getElementById("eventsList");
            if (eventsList) {
                eventsList.innerHTML = "";
                ['selected', 'births', 'deaths', 'events', 'holidays'].forEach(function(category) {
                    if (data[category]) {
                        data[category].forEach(function(item) {
                            var listItem = document.createElement("li");
                            listItem.innerHTML = "<strong>" + (item.year || '') + ":</strong> " + item.text;
                            
                            if (item.pages) {
                                var pagesList = document.createElement("ul");
                                item.pages.forEach(function(page) {
                                    var pageItem = document.createElement("li");
                                    pageItem.innerHTML = "Article: " + page.titles.display +
                                                         "<br>Description: " + page.description +
                                                         "<br><a href='" + page.content_urls.desktop.page + "'>Read more</a>";
                                    if (page.thumbnail && page.thumbnail.source) {
                                        var img = document.createElement("img");
                                        img.src = page.thumbnail.source;
                                        img.alt = "Image for " + page.titles.display;
                                        img.style.width = '100px';
                                        pageItem.appendChild(img);
                                    }
                                    pagesList.appendChild(pageItem);
                                });
                                listItem.appendChild(pagesList);
                            }
                            eventsList.appendChild(listItem);
                        });
                    }
                });
            }
        }

        window.onload = fetchEvents;
    </script>
<?php }

function display_historical_events() {
    return '<h1>Ιστορικά Γεγονότα "Σαν Σήμερα"</h1><div id="eventsList"></div>';
}

add_action('wp_enqueue_scripts', 'historical_events_enqueue_script');
add_shortcode('historical_events', 'display_historical_events');
?>
