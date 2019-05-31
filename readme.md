# 快捷复制指定文本内容

目前接入纯文本复制；  
有合适的展示方案后，接入富文本复制；  

在富文本编辑器中插入
```html
[os-copy]
需要被复制的内容
[/os-copy]
```
或在html源码中插入
```html
<p>[os-copy]</p>需要被复制的任意内容<p>[/os-copy]</p>
```

复制功能基于clipboard.js <a href="https://github.com/lgarron/clipboard-polyfill" target="_blank">https://github.com/lgarron/clipboard-polyfill</a>
