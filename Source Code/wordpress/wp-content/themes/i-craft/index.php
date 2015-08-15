<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme and one of the
 * two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * For example, it puts together the home page when no home.php file exists.
 *
 * @link http://codex.wordpress.org/Template_Hierarchy
 *
 * @package i-craft
 * @since i-craft 1.0
 */

get_header(); ?>
<style>
.site-main {
    padding-bottom: 1%;
}
</style>

<!-- 메인페이지 미들 영역 -->
<blockquote style="border-left: 5px solid #ce4844;">
  <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat a ante.</p>
  <footer>Someone famous in <cite title="Source Title">Source Title</cite></footer>
</blockquote>

<div class="bs-example" data-example-id="thumbnails-with-custom-content">
    <div class="row">
      <div class="col-sm-6 col-md-4">
        <div class="thumbnail">
          <img data-src="holder.js/100%x200" alt="100%x200" src="http://61.252.147.56/wp-content/uploads/2015/08/011.jpg" data-holder-rendered="true" style="height: 200px; width: 100%; display: block;">
          <div class="caption">
            <h3>KPOP 전문 댄서</h3>
            <p>KPOP의 장르별 전문 안무가팀의 정확한 안무동작으로 안무 정확성을 높였습니다.</p><br/>
            <p><a href="http://61.252.147.56/index.php/equipment-information/" class="btn btn-danger" role="button" style="color:white">Button</a></p>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-md-4">
        <div class="thumbnail">
          <img data-src="holder.js/100%x200" alt="100%x200" src="http://61.252.147.56/wp-content/uploads/2015/08/021.jpg" data-holder-rendered="true" style="height: 200px; width: 100%; display: block;">
          <div class="caption">
            <h3>최고의 장비</h3>
            <p>댄서의 순간의 모션과 역학 관계를 정밀하게 포착하기 위해 최신의 장비와 소프트웨어를 사용하여 양질의 데이터를 생산하고 있습니다.</p>
            <p><a href="http://61.252.147.56/index.php/equipment-information/" class="btn btn-danger" role="button" style="color:white">Button</a></p>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-md-4">
        <div class="thumbnail">
          <img data-src="holder.js/100%x200" alt="100%x200" src="http://61.252.147.56/wp-content/uploads/2015/08/03.png" data-holder-rendered="true" style="height: 200px; width: 100%; display: block;">
          <div class="caption">
            <h3>전용 스튜디오</h3>
            <p>댄스의 모션과 역학 데이터의 전문화된 데이터를 추출할 수 있는 KPOP 모션/생체 캡쳐 전문 스튜디오를 운영하고 있습니다.</p>
            <p><a href="http://61.252.147.56/index.php/equipment-information/" class="btn btn-danger" role="button" style="color:white">Button</a></p>
          </div>
        </div>
      </div>
    </div>
  </div>
<!-- 메인페이지 미들 영역 끝 -->


<br/><br/>

<!-- Youtube Section -->
<blockquote style="border-left: 5px solid #ce4844;">
  <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat a ante.</p>
  <footer>Someone famous in <cite title="Source Title">Source Title</cite></footer>
</blockquote>



        <?php if ( have_posts() ) :  ?>
	<div class="bs-example" data-example-id="thumbnails-with-custom-content">
	<div class="row">
	
	<?php while ( have_posts() ) : the_post(); ?>
	<div class="col-sm-6 col-md-4">
	<div class="thumbnail">

	<?php get_template_part( 'content', get_post_format() ); ?>
	</div></div>
	<?php endwhile; ?>

	<?php icraft_paging_nav(); ?>
	</div></div>
	<?php else : ?>
	<?php get_template_part( 'content', 'none' ); ?>

	<?php endif; ?>

        
	

<!-- End of the Youtube Section -->
<!-- #primary -->

<br/><br/><br/>

<!-- Company Information -->

<tr>
<td style="text-align:left;padding-left:20px">
<img src="http://61.252.147.56/wp-content/uploads/2015/08/logo1.png">
(152-768) 서울시 구로구 구로동 235 한신IT 1101호 | 대표이사: 성원용 사업자등록번호: 119-81-37208 | <strong>Tel:</strong> (02)2108-8123 | <strong>Fax</strong> (02) 2108-8120
</td>
</tr>
</table>
<!-- End of the Company Information -->
<?php get_footer(); ?>