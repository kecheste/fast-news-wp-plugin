<?php

class Fast_News_API
{
    public function fast_news_api_request()
    {
        // convert the api key with your's... I put mine here because there's no one who can access it
        $url = 'https://newsapi.org/v2/top-headlines?country=us&category=business&apiKey=ad4490eb5b0d4911aac9580b11ecfd82';
        $response = wp_remote_get(
            $url,
            array(
                'timeout' => 120,
                'httpVersion' => '1.1',
                'headers' => array(
                    'User-Agent' => 'FastBusinessNews/1.0',
                ),
            )
        );
        if(!$response || is_wp_error($response)) {
            return false;
        }
        $res = wp_remote_retrieve_body($response);
        $response = json_decode($res);
        
        return $response;
    }

    public function fast_news_display() {
        // this function just calls a function to display the fetched news
        return $this->fast_business_news_display();
    }

    public function display_hello_village() {
        return '<h1>Hello Village</h1>';
    }

    public function no_article_error_display() {
        /// this error will display when there's no article with the given category or keyword... I tested it by changing my category, 'business'
        return 
        '<div class="err-container">
            <p class="error-msg">Sorry, no news articles available right now.</p>
        </div>';
    }

    public function error_display($message = '') {
        // this error will display when there's any error fetching the data or after fetching the data, when the status is not ok
        return 
        '<div class="err-container">
            <p class="error-msg">Something went wrong.</p>
            <p class="error-content">' . $message . '</p>
        </div>';
    }

    public function fast_business_news_display() {
        ob_start();
    
        $response = $this->fast_news_api_request();
        
        if ($response) {

            if($response->status != 'ok') {
                return $this->error_display($response->message);
            }
            
            $articles = $response->articles;
            if (!empty($articles)) {
                ?>
<div class="news-container">
    <?php
                foreach ($articles as $article) {
                    ?>
    <div class="news-card">
        <h2 class="news-title"><?php echo esc_html($article->title); ?></h2>
        <?php if (!empty($article->urlToImage)) { ?>
        <img class="news-img" src="<?php echo esc_url($article->urlToImage); ?>"
            alt="<?php echo esc_attr($article->title); ?>">
        <?php } ?>
        <p><?php echo esc_html($article->description); ?></p>
        <div class="footer">
            <a href="<?php echo esc_url($article->url); ?>" target="_blank">Read more</a>
            <p class="author">By: <?php echo esc_html($article->author); ?></p>
        </div>
    </div>
    <?php
                }
                ?>
</div>
<?php
            } else {
                return $this->no_article_error_display();
            }
        } else {
            return $this->no_article_error_display();
        }
    
        return ob_get_clean();
    }

}