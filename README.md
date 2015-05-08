# Simple Comment Box

## How to Install

- import the .sql file to your database
- configure database settings in db-comment.php
- configure API url in imComment.js
- insert this html code to wherever you want this comment box (A plugin for Mediawiki was made for your convenience).

``` html
<div id="imCommentBox">
<div id="comment-hint" style="">Submitting Comment...</div>
	<input name="articleID" value="<!--put your unique article ID HERE-->" type="hidden" id="imArticleID" />
	<span>Loading</span>
</div>

<div id="comment-startflag"></div>
<div id="comment-endflag"></div>

<script type="text/javascript" src="https://YOURAPIURL/imComment.js"></script>
```



## Demo

[My Blog](https://note.masterchan.me)




## License

Copyright [2015] [masterchan]

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

    http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
