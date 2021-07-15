<?php
/**
 * Форма отправки отзыва
 * 
 * @author WPPW
 * @link http://wppw.ru
 * **************************** */
defined( 'ABSPATH' ) or exit;
?>

<div class="b-wrapper-content" id="modal-product_review" style="display: none;overflow-x: hidden;">
	<form class="b-review" id="wppw_review_form">
		<div class="b-review-block">
			<div class="b-review-title">
				<h4>Мой отзыв о <?= get_the_title() ?></h4>
			</div>
			<div class="b-review-item">
				<div class="b-review-item__left">
					<strong>Общая оценка</strong>
				</div>
				<div class="b-reviews-item__right">
					<div class="b-review-item__general">
						<div class="star-rating">
							<input type="radio" name="rating" class="rating" value="1" checked />
							<input type="radio" name="rating" class="rating" value="2" checked />
							<input type="radio" name="rating" class="rating" value="3" checked />
							<input type="radio" name="rating" class="rating" value="4" checked />
							<input type="radio" name="rating" class="rating" value="5" />
						</div>
						<span>Отлично</span>									
					</div>
				</div>
			</div>
			<div class="b-review-item">
				<div class="b-review-item__left">
					<strong>Опыт использования</strong>
				</div>
				<div class="b-reviews-item__right">
					<div class="b-reviews-item__radios">
						<div class="b-reviews-radio">
							<input type="radio" checked="" id="r1" name="experience" value="1" />
							<label for="r1"><span>Меньше месяца</span></label>  
						</div>
						<div class="b-reviews-radio">
							<input type="radio" id="r2" name="experience" value="2" />
							<label for="r2"><span>Несколько месяцев</span></label>  
						</div>
						<div class="b-reviews-radio">
							<input type="radio" id="r3" name="experience" value="3" />
							<label for="r3"><span>Больше года</span></label>  
						</div>																				
					</div>
				</div>
			</div>
			<div class="b-review-item align-items-start">
				<?php /* div class="b-review-item__left">
				  <strong>Оценки по параметрам</strong>
				  </div>
				  <div class="b-reviews-item__right">
				  <div class="b-review-item__block">
				  <span>Изображение</span>
				  <div class="star-rating">
				  <input type="radio" name="a1" class="rating" value="1" checked />
				  <input type="radio" name="a2" class="rating" value="2" checked />
				  <input type="radio" name="a3" class="rating" value="3" checked />
				  <input type="radio" name="a4" class="rating" value="4" checked />
				  <input type="radio" name="a5" class="rating" value="5" />
				  </div>
				  </div>
				  <div class="b-review-item__block">
				  <span>Звук</span>
				  <div class="star-rating">
				  <input type="radio" name="a1" class="rating" value="1" checked />
				  <input type="radio" name="a2" class="rating" value="2" checked />
				  <input type="radio" name="a3" class="rating" value="3" checked />
				  <input type="radio" name="a4" class="rating" value="4" checked />
				  <input type="radio" name="a5" class="rating" value="5" />
				  </div>
				  </div>
				  <div class="b-review-item__block">
				  <span>Удобство</span>
				  <div class="star-rating">
				  <input type="radio" name="a1" class="rating" value="1" checked />
				  <input type="radio" name="a2" class="rating" value="2" checked />
				  <input type="radio" name="a3" class="rating" value="3" checked />
				  <input type="radio" name="a4" class="rating" value="4" checked />
				  <input type="radio" name="a5" class="rating" value="5" />
				  </div>
				  </div>
				  </div */ ?>
			</div>
			<div class="b-review-item">
				<div class="b-review-item__left">
					<strong>Порекомендовали бы 
						друзьям?</strong>
				</div>
				<div class="b-reviews-item__right">
					<div class="b-reviews-item__radios">
						<div class="b-reviews-radio">
							<input type="radio" checked="" id="r4" name="is_recommend" value="1" />
							<label for="r4"><span>Да</span></label>  
						</div>
						<div class="b-reviews-radio">
							<input type="radio" id="r5" name="is_recommend" value="0" />
							<label for="r5"><span>Нет</span></label>  
						</div>																				
					</div>
				</div>
			</div>
		</div>

		<div class="b-review-block b-review-block__write">
			<div class="b-review-title">
				<h4>Расскажите подробнее</h4>
			</div>
			<div class="b-review-item align-items-start">
				<div class="b-review-item__left">
					<strong>Достоинства</strong>
				</div>
				<div class="b-reviews-item__right">
					<textarea class="b-reviews-item__textarea" name="worth" placeholder="Что вам понравилось?" required></textarea>
				</div>
			</div>
			<div class="b-review-item align-items-start">
				<div class="b-review-item__left">
					<strong>Недостатки</strong>
				</div>
				<div class="b-reviews-item__right">
					<textarea class="b-reviews-item__textarea" name="disatvantages" placeholder="Что не оправдало ожиданий" required></textarea>
				</div>
			</div>
			<div class="b-review-item align-items-start">
				<div class="b-review-item__left">
					<strong>Комментарий</strong>
				</div>
				<div class="b-reviews-item__right">
					<textarea class="b-reviews-item__textarea" name="comment" placeholder="Впечатления о товаре" required></textarea>
				</div>
			</div>
			<div class="b-review-item align-items-start">
				<div class="b-review-item__left">
					<strong>Ваше имя</strong>
				</div>
				<div class="b-reviews-item__right">
					<textarea class="b-reviews-item__textarea" name="name" placeholder="Имя" required></textarea>
				</div>
			</div>
			<?php /* TODO div class="b-review-item align-items-start">
			  <div class="b-review-item__left">
			  <strong>Фотографии товара</strong>
			  </div>
			  <div class="b-reviews-item__right">
			  <div class="b-foto">
			  <input type="file">
			  <div class="b-foto-item">
			  <div class="b-foto-item__img icon-camera"></div>
			  <p><span>Нажмите на ссылку</span>, чтобы выбрать фотографии или
			  просто перетащите их сюда</p>
			  </div>
			  </div>
			  </div>
			  </div */ ?>

			<div class="b-review-item b-review-item__last">
				<div class="b-review-item__left">

				</div>
				<div class="b-reviews-item__right">
					<div class="b-check">
						<input type="checkbox" checked id="a1" name="is_anonymouse" />
						<label for="a1"><span></span><i><b>Оставить отзыв анонимно</b>По умолчанию отзыв будет опубликован от вашего имени.<br/>
								Отметьте эту опцию, если вы хотите опубликовать отзыв анонимно.</i></label>  
					</div>
					<div class="b-reviews-item_wrapper">
						<input type="hidden" name="post_id" value="<?= get_the_ID() ?>">
						<input type="hidden" name="nonce" value="<?= wp_create_nonce( WPPW_NONCE ) ?>">
						<button class="red-btn b-reviews-item__btn">Оставить отзыв</button>
						<p>Нажав «Отправить отзыв», вы соглашаетесь<br/>
							c условиями использования </p>
					</div>
				</div>
			</div>																									

		</div>


	</form> 						
</div>