{{ config_load file="{{ $gimme->language->english_name }}.conf" }}

{{ include file="_tpl/_html-head.tpl" }}

	<div id="wrapper">

{{ include file="_tpl/header.tpl" }}

		<div id="content" class="clearfix">
            
            <section class="main entry page">
            
            	<article>
                    <h2>{{ #aboutUs# }}</h2>
                	<figure>
                    	<img src="pictures/article-img-grid-1.jpg" alt="">
                        <p><em>Source</em> / All images made by Sean McGrath released as CC</p>
                    </figure>
                    <p>Lorem Ipsum has been the industry's <strong>standard dummy</strong> text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. Lorem Ipsum has been the industry's standard dummy* text ever since the 1500s.<br />
                    It has survived not only five centuries, but also the leap into <a href="#">electronic typesetting</a>, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages...</p>
                    <blockquote>
                    	<p>Lorem Ipsum has been the industry's standard dummy text ever since the 1500s,<br />
                        when an unknown printer took a galley of type</p>
                    </blockquote>
                    <p>It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged...</p>
                    <ul>
                    	<li>Some people need a list</li>
                        <li>And at this point you know this is</li>
                        <li>The third looks the same</li>
                    </ul>
                    <p>Lorem Ipsum has been the industry's standard dummy text ever since the 1500s.</p>
                    <p>Lorem Ipsum has been the industry's <strong>standard dummy</strong> text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. Lorem Ipsum has been the industry's standard dummy* text ever since the 1500s.<br />
                    It has survived not only five centuries, but also the leap into <a href="#">electronic typesetting</a>, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages...</p>
                    <p>It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged...</p>
                    <p>Lorem Ipsum has been the industry's standard dummy text ever since the 1500s.</p>
                    <p>Lorem Ipsum has been the industry's <strong>standard dummy</strong> text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. Lorem Ipsum has been the industry's standard dummy* text ever since the 1500s.<br />
                    It has survived not only five centuries, but also the leap into <a href="#">electronic typesetting</a>, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages...</p>
                    <p>It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged...</p>
                </article>
            
            </section><!-- / Entry -->
            
            <aside>
            
            	<h2>{{ #contactUs# }}</h2>
                <div class="aside-box">
                	<img src="pictures/map-sample-small.png" alt="" />
                    <p>We're always happy to hear from you. You can contact us via email at<br />
                    <a href="mailto:contact@sourcefabric.org">contact@sourcefabric.org</a></p>
                    
                    <h3>POSTAL ADDRESS:</h3>
                    <p>Sourcefabric o.p.s.<br />
                    Salvátorská 10<br />
                    110 00 Praha 1<br />
                    Czech Republic</p>
                    
                    <h3>TORONTO OFFICE:</h3>
                    <p>Sourcefabric<br />
                    Centre for Social Innovation<br />
                    720 Bathurst St. Suite 203<br />
                    Toronto, Ontario<br />
                    M5S 2R4<br />
                    Canada</p>
                    
                    <h3>BERLIN OFFICE:</h3>
                    <p>Sourcefabric<br />
                    Prinzessinnenstraße 20<br />
                    Aufgang A<br />
                    10969 Berlin<br />
                    Germany<br />
                    +49 (0)30 44044999</p>
                    
                    <p>If you need technical support, please visit our Get <a href="#">Help pages</a>. We also frequent our <a href="#">forums</a> on a very regular basis; a post there will get a quick answer!</p>
                    
                    <ul class="list-large">
                    	<li><h2><a href="#">Rs <span>Credits</span></a></h2></li>
                    	<li><h2><a href="#">Rs <span>Marketing</span></a></h2></li>
                    </ul>
                    
                </div>
				            
            </aside><!-- / Aside -->
            
            <div class="divider"></div>
        
        </div><!-- / Content -->
        
{{ include file="_tpl/footer.tpl" }}
    
    </div><!-- / Wrapper -->
	
{{ include file="_tpl/_html-foot.tpl" }}

</body>
</html>