<?php
class ModelSocialAutoPilotReview extends Model {
	public function getShareInfo($review_id) {
		$share_info = array();

		$review_info = $this->getReview($review_id);

		if ($review_info) {
			$this->load->model('social_autopilot/product');

			$product_share_info = $this->model_social_autopilot_product->getShareInfo($review_info['product_id']);

			if ($product_share_info) {
				$share_info['image'] = $product_share_info['image'];
				$share_info['link'] = $product_share_info['link'];
				$share_info['title'] = $product_share_info['title'];
				$share_info['short_description'] = $product_share_info['short_description'];
			}

			$share_info['message'] = $this->getMessage($review_info);
		}

		return $share_info;
	}

	private function getReview($review_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "review WHERE review_id = '" . (int)$review_id  . "' AND status = '1'");

		if ($query->num_rows) {
			return array(
				'review_id'   => $query->row['review_id'],
				'product_id'  => $query->row['product_id'],
				'author'      => $query->row['author'],
				'text'        => $query->row['text'],
				'rating'      => $query->row['rating'],
				'date_added'  => $query->row['date_added']
			);
		} else {
			return false;
		}
	}

	private function getMessage($review_info, $template_id = 0) {
		$message = '';

		$this->load->model('social_autopilot/template');

		if ($template_id) {
			$template_info = $this->model_social_autopilot_template->getTemplate($template_id);
		} else {
			$template_info = $this->model_social_autopilot_template->getDefaultTemplateByCategoryCode('review');
		}

		if ($template_info) {
			$find = array(
				'{review.author}',
				'{review.text}',
				'{review.rating}',
				'{review.rating.stars}',
				'{review.date_added}'
			);

			$replace = array(
				'review.author'       => html_entity_decode($review_info['author'], ENT_QUOTES, 'UTF-8'),
				'review.text'         => html_entity_decode($review_info['text'], ENT_QUOTES, 'UTF-8'),
				'review.rating.'      => $review_info['rating'],
				'review.rating.stars' => html_entity_decode(str_repeat(str_replace('&amp;', '&', $this->config->get('social_autopilot_rating_star_code')), $review_info['rating']), ENT_QUOTES, 'UTF-8'),
				'review.date_added'   => date($this->language->get('date_format_short'), strtotime($review_info['date_added']))
			);

			$message = str_replace($find, $replace, html_entity_decode($template_info['message'], ENT_QUOTES, 'UTF-8'));
		}

		return $message;
	}

	public function getLastId() {
		$query = $this->db->query("SELECT review_id FROM " . DB_PREFIX . "review ORDER BY review_id DESC LIMIT 0,1");

		return $query->row['review_id'];
	}
}
