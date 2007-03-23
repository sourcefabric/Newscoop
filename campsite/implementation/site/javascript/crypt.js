b64s='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_"'

function textToBase64(t) {
  var r=''; var m=0; var a=0; var tl=t.length-1; var c
  for(n=0; n<=tl; n++) {
    c=t.charCodeAt(n)
    r+=b64s.charAt((c << m | a) & 63)
    a = c >> (6-m)
    m+=2
    if(m==6 || n==tl) {
      r+=b64s.charAt(a)
      if((n%45)==44) {r+="\n"}
      m=0
      a=0
    }
  }
  return r
} // textToBase64

function rc4(key, text) {
  var i, x, y, t, x2, kl=key.length;
  s=[];

  for (i=0; i<256; i++) s[i]=i
  y=0
  for(j=0; j<2; j++) {
    for(x=0; x<256; x++) {
      y=(key.charCodeAt(x%kl) + s[x] + y) % 256
      t=s[x]; s[x]=s[y]; s[y]=t
    }
  }
  var z=""
  for (x=0; x<text.length; x++) {
    x2=x & 255
    y=( s[x2] + y) & 255
    t=s[x2]; s[x2]=s[y]; s[y]=t
    z+= String.fromCharCode((text.charCodeAt(x) ^ s[(s[x2] + s[y]) % 256]))
  }
  return z
} // rc4

function rc4encrypt(k,p) {
  return textToBase64(rc4(k,p))
} // rc4encrypt
