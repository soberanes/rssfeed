<?php
/**
 * rss (simple rss 2.0 feed creator php class)
 *
 * @author      Paul Soberanes <paul@neubox.net>
 * @copyright   2017 (c) Neubox.com
 * @version     1.0.0 (Abril 3, 2017)
 */
class rss
{

    /**
     * @param array $a_db   database settings
     * @param string $xmlns XML namespace
     * @param array $a_channel channel properties
     * @param string $site_url the URL of your site
     * @param string $site_name the name of your site
     * @param bool $full_feed flag for full feed (all topic content)
     */
    function __construct($a_db, $xmlns, $a_channel, $site_url, $site_name, $full_feed = false){
        //Initialize params
        $this->db_settings = $a_db;
        $this->xmlns = ($xmlns ? '' . $xmlns : '');
        $this->channel_properties = $a_channel;
        $this->site_url = $site_url;
        $this->site_name = $site_name;
        $this->full_feed = $full_feed;
    }

    /**
     * Generate RSS 2.0 feed
     *
     * @return string RSS 2.0 xml
     */
    public function create_feed(){

        $xml = new DOMDocument("1.0", "UTF-8");
        //rss element
        $xml_rss = $xml->createElement('rss');
        $xml_rss->setAttribute('version','2.0');
        $xml_rss = $xml->appendChild($xml_rss);

        //channel element
        $xml_channel = $xml->createElement('channel');
        $xml_channel = $xml_rss->appendChild($xml_channel);

        //title elemente
        $xml_title = $xml->createElement('title', $this->channel_properties["title"]);
        $xml_title = $xml_channel->appendChild($xml_title);

        //link elemente
        $xml_link = $xml->createElement('link', $this->channel_properties["link"]);
        $xml_link = $xml_channel->appendChild($xml_link);
        //description elemente
        $xml_description = $xml->createElement('description', $this->channel_properties["description"]);
        $xml_description = $xml_channel->appendChild($xml_description);

        //channel optional properties
        if(array_key_exists("language", $this->channel_properties)){
            $xml_language = $xml->createElement('language', $this->channel_properties["language"]);
            $xml_language = $xml_channel->appendChild($xml_language);
        }
        //image element
        if(array_key_exists("image_title", $this->channel_properties)){
            $xml_image = $xml->createElement('image');
            $xml_image = $xml_channel->appendChild($xml_image);

            $xml_image->appendChild($xml->createElement('title', $this->channel_properties["image_title"]));
            $xml_image->appendChild($xml->createElement('link', $this->channel_properties["image_link"]));
            $xml_image->appendChild($xml->createElement('url', $this->channel_properties["image_url"]));
        }

        //get RSS channel items
        $now = date("Y-m-d H:i:s"); //get current time
        $rss_items = $this->get_feed_items($now);

        foreach($rss_items as $rss_item){

            $item = $xml->createElement('item');
            $item = $xml_channel->appendChild($item);
            $item->appendChild($xml->createElement('title', $rss_item['title']));
            $item->appendChild($xml->createElement('link', $rss_item['link']));
            $item->appendChild($xml->createElement('description', $rss_item['description']));
            $item->appendChild($xml->createElement('pubDate', $rss_item['pubDate']));
            $item->appendChild($xml->createElement('category', $rss_item['category']));
            $item->appendChild($xml->createElement('source', $this->site_name));

            if($this->full_feed) {
                //content complete, not excerpt
            }
        }
        return $xml->saveXML();
    }

    /**
     * Get RSS channel items
     *
     * @param $rss_date
     * @param $rss_items_count
     * @internal param $rss_items
     * @return array
     */
    public function get_feed_items($rss_date, $rss_items_count = 10){
        //connect to database
        $conn = new mysqli(
            $this->db_settings["db_server"],
            $this->db_settings["db_user"],
            $this->db_settings["db_passwd"],
            $this->db_settings["db_name"]
        );

        //check connection
        if($conn->connect_error){
            trigger_error('Database connection failed: '.$conn->connect_error, E_USER_ERROR);
        }

        //create array with topic IDs
        $a_topic_ids = array();
        $sql = 'SELECT id FROM topics '.
            'WHERE pubDate <='."'".$conn->real_escape_string($rss_date)."' ".
            'AND pubDate IS NOT NULL '.
            'ORDER BY pubDate DESC '.
            'LIMIT 0,'.$rss_items_count;

        $rs = $conn->query($sql);
        if($rs === false){
            $user_error = 'Wrong SQL: '.$sql.'<br>'.'Error: '.$conn->errno.' '.$conn->error;
            trigger_error($user_error, E_USER_ERROR);
        }
        $rs->data_seek(0);
        while ($res = $rs->fetch_assoc()) {
            array_push($a_topic_ids, $res['id']);
        }
        $rs->free();

        //get rss items according to http://www.rssboard.org/rss-specification
        $a_rss_items = array();
        $a_rss_item  = array();
        $topic = array();
        foreach ($a_topic_ids as $topic_id) {
            //get topic properties
            $sql = 'SELECT * FROM topics WHERE id = '.$topic_id;
            $rs = $conn->query($sql);

            if($rs === false){
                trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $conn->error, E_USER_ERROR);
            }else{
                $rs->data_seek(0);
                $topic = $rs->fetch_array(MYSQLI_ASSOC);
            }

            //title
            $a_rss_item['title'] = $topic['title'];

            //link
            $a_rss_item['link'] = $topic['link'];

            //description
            $a_rss_item['description'] = '';

            if($topic['image']){
                $img_url = $topic['image'];
                $a_rss_item['description'] = '<img src="'.$img_url.'" hspace="5" vspace="5" align="left"/>';
            }
            $a_rss_item['description'] .= $topic['description'];

            //pubDate
            $date = new DateTime($topic['pubDate']);
            $a_rss_item['pubDate'] = $date->format("D, d M Y H:i:s 0");

            //category
            $a_rss_item['category'] = $topic['category'];

            //source
            $a_rss_item['source'] = $this->site_name;

            array_push($a_rss_items, $a_rss_item);
        }

        return $a_rss_items;
    }

}
