        </div><!-- .container -->
    </main><!-- .site-content -->
    
    <!-- 푸터 영역 -->
    <footer class="site-footer">
        <div class="container">
            <div class="footer-widgets">
                <!-- 회사 정보 -->
                <div class="footer-widget">
                    <h4 class="widget-title"><?= trans('footer.about_us') ?></h4>
                    <p><?= trans('footer.company_desc') ?></p>
                    <div class="social-links">
                        <a href="#" target="_blank" aria-label="Facebook"><i class="icon-facebook"></i></a>
                        <a href="#" target="_blank" aria-label="Twitter"><i class="icon-twitter"></i></a>
                        <a href="#" target="_blank" aria-label="Instagram"><i class="icon-instagram"></i></a>
                        <a href="#" target="_blank" aria-label="LinkedIn"><i class="icon-linkedin"></i></a>
                    </div>
                </div>
                
                <!-- 빠른 링크 -->
                <div class="footer-widget">
                    <h4 class="widget-title"><?= trans('footer.quick_links') ?></h4>
                    <ul class="footer-links">
                        <li><a href="<?= base_url() ?>/about"><?= trans('nav.about') ?></a></li>
                        <li><a href="<?= base_url() ?>/services"><?= trans('nav.services') ?></a></li>
                        <li><a href="<?= base_url() ?>/contact"><?= trans('nav.contact') ?></a></li>
                        <li><a href="<?= base_url() ?>/privacy"><?= trans('footer.privacy') ?></a></li>
                        <li><a href="<?= base_url() ?>/terms"><?= trans('footer.terms') ?></a></li>
                    </ul>
                </div>
                
                <!-- 연락처 정보 -->
                <div class="footer-widget">
                    <h4 class="widget-title"><?= trans('footer.contact_us') ?></h4>
                    <address>
                        <p><i class="icon-map-marker"></i> <?= trans('footer.address') ?></p>
                        <p><i class="icon-phone"></i> <?= trans('footer.phone') ?></p>
                        <p><i class="icon-envelope"></i> <a href="mailto:info@topmkt.co.kr">info@topmkt.co.kr</a></p>
                    </address>
                </div>
                
                <!-- 뉴스레터 구독 -->
                <div class="footer-widget">
                    <h4 class="widget-title"><?= trans('footer.newsletter') ?></h4>
                    <p><?= trans('footer.newsletter_desc') ?></p>
                    <form class="newsletter-form" action="<?= base_url() ?>/subscribe" method="post">
                        <input type="email" name="email" placeholder="<?= trans('footer.your_email') ?>" required>
                        <button type="submit" class="btn btn-primary"><?= trans('footer.subscribe') ?></button>
                    </form>
                </div>
            </div>
            
            <!-- 저작권 정보 -->
            <div class="footer-bottom">
                <div class="copyright">
                    &copy; <?= date('Y') ?> <?= trans('footer.copyright', ['year' => date('Y')]) ?>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- JavaScript 파일 -->
    <script src="<?= base_url() ?>/assets/js/app.js"></script>
    
    <!-- 언어 변경 스크립트 -->
    <script>
    function changeLanguage(locale) {
        window.location = '<?= base_url() ?>/change-language?locale=' + locale + '&redirect=' + encodeURIComponent(window.location.pathname);
    }
    </script>
</body>
</html> 