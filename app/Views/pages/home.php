<!-- 홈페이지 내용 -->
<section class="hero-section">
    <div class="hero-content">
        <h1><?= trans('home.hero_title') ?></h1>
        <p><?= trans('home.hero_subtitle') ?></p>
        <div class="hero-buttons">
            <a href="<?= base_url() ?>/services" class="btn btn-primary"><?= trans('home.learn_more') ?></a>
            <a href="<?= base_url() ?>/contact" class="btn btn-outline"><?= trans('home.contact_us') ?></a>
        </div>
    </div>
    <div class="hero-image">
        <img src="<?= base_url() ?>/assets/images/hero-image.jpg" alt="<?= trans('home.hero_image_alt') ?>">
    </div>
</section>

<!-- 서비스 소개 섹션 -->
<section class="services-section">
    <div class="section-header">
        <h2><?= trans('home.our_services') ?></h2>
        <p><?= trans('home.services_subtitle') ?></p>
    </div>
    
    <div class="services-grid">
        <!-- 서비스 항목 1 -->
        <div class="service-card">
            <div class="service-icon">
                <i class="icon-digital-marketing"></i>
            </div>
            <h3><?= trans('services.digital_marketing') ?></h3>
            <p><?= trans('services.digital_marketing_desc') ?></p>
            <a href="<?= base_url() ?>/services/digital-marketing" class="btn-text"><?= trans('home.read_more') ?></a>
        </div>
        
        <!-- 서비스 항목 2 -->
        <div class="service-card">
            <div class="service-icon">
                <i class="icon-seo"></i>
            </div>
            <h3><?= trans('services.seo') ?></h3>
            <p><?= trans('services.seo_desc') ?></p>
            <a href="<?= base_url() ?>/services/seo" class="btn-text"><?= trans('home.read_more') ?></a>
        </div>
        
        <!-- 서비스 항목 3 -->
        <div class="service-card">
            <div class="service-icon">
                <i class="icon-content"></i>
            </div>
            <h3><?= trans('services.content_marketing') ?></h3>
            <p><?= trans('services.content_marketing_desc') ?></p>
            <a href="<?= base_url() ?>/services/content-marketing" class="btn-text"><?= trans('home.read_more') ?></a>
        </div>
    </div>
</section>

<!-- 최근 소식 섹션 -->
<section class="news-section">
    <div class="section-header">
        <h2><?= trans('home.latest_news') ?></h2>
        <p><?= trans('home.news_subtitle') ?></p>
    </div>
    
    <div class="news-grid">
        <!-- 뉴스 항목은 데이터베이스에서 가져올 예정 -->
        <?php for ($i = 0; $i < 3; $i++): ?>
            <div class="news-card">
                <div class="news-image">
                    <img src="<?= base_url() ?>/assets/images/placeholder.jpg" alt="뉴스 이미지">
                </div>
                <div class="news-content">
                    <div class="news-date">2023-05-<?= sprintf('%02d', $i + 10) ?></div>
                    <h3>샘플 뉴스 제목 <?= $i + 1 ?></h3>
                    <p>이것은 샘플 뉴스 내용입니다. 실제 데이터는 데이터베이스에서 가져올 예정입니다.</p>
                    <a href="<?= base_url() ?>/news/sample-<?= $i + 1 ?>" class="btn-text"><?= trans('home.read_more') ?></a>
                </div>
            </div>
        <?php endfor; ?>
    </div>
    
    <div class="section-footer">
        <a href="<?= base_url() ?>/news" class="btn btn-outline"><?= trans('home.view_all_news') ?></a>
    </div>
</section>

<!-- 고객 후기 섹션 -->
<section class="testimonials-section">
    <div class="section-header">
        <h2><?= trans('home.testimonials') ?></h2>
        <p><?= trans('home.testimonials_subtitle') ?></p>
    </div>
    
    <div class="testimonials-slider">
        <!-- 슬라이더는 JavaScript로 구현 예정 -->
        <div class="testimonial">
            <div class="testimonial-content">
                <p>"탑마케팅의 서비스는 우리 회사의 디지털 마케팅 전략을 획기적으로 변화시켰습니다. 전문적인 접근과 데이터 기반의 전략이 인상적입니다."</p>
            </div>
            <div class="testimonial-author">
                <img src="<?= base_url() ?>/assets/images/avatar1.jpg" alt="고객 이미지">
                <div class="author-info">
                    <h4>김철수</h4>
                    <p>ABC 회사 CEO</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA 섹션 -->
<section class="cta-section">
    <div class="cta-content">
        <h2><?= trans('home.cta_title') ?></h2>
        <p><?= trans('home.cta_subtitle') ?></p>
        <a href="<?= base_url() ?>/contact" class="btn btn-primary btn-lg"><?= trans('home.get_started') ?></a>
    </div>
</section> 