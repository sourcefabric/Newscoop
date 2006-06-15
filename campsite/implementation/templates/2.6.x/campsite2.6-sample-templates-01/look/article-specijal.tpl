<td>
<p class="nadnaslov"><!** print article nadnaslov></p>
             <p class="main-naslov"><!** print article name></p>
             <!** if article podnaslov not "">
             <!** if image 2>
             <div style="float:right; margin: 5px;><img src="/cgi-bin/get_img?<!** urlparameters image 2>" width="245"><br/><span class="caption"><!** print image 2 description></span></div>
             <!** endif>
             <p class="podnaslov"><!** print article podnaslov></p>
             <!** endif>
             <!** if article autor not "">
             <p class="blok-podnaslov"><!** print article autor></p>
             <!** endif>
             <p class="tekst"><!** print article intro></p>
<!** if allowed>
             <p class="tekst"><!** print article tekst></p>
             <!** if article antrfile not "">
             <div style="background-color:#E4EEF8;"><p class="tekst"><!** print article antrfile></p></div>
<!** else>
<p class="footer">Ukoliko želite da pročitate ceo tekst morate biti pretplaćeni. To možete uraditi ovde...</p>
<!** endif>
             <!** endif>
</td>