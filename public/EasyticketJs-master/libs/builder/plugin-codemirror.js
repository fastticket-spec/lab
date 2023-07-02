Easyticket.CodeEditor = {
	
	isActive: false,
	oldValue: '',
	doc:false,
	codemirror:false,
	
	init: function(doc) {

		if (this.codemirror == false)		
		{
			this.codemirror = CodeMirror.fromTextArea(document.querySelector("#easyticket-code-editor textarea"), {
				mode: 'text/html',
				lineNumbers: true,
				autofocus: true,
				lineWrapping: true,
				//viewportMargin:Infinity,
				theme: 'material'
			});
			
			this.isActive = true;
			this.codemirror.getDoc().on("change", function (e, v) { 
				if (v.origin != "setValue")
				delay(Easyticket.Builder.setHtml(e.getValue()), 1000);
			});
		}
		
		
		//_self = this;
		Easyticket.Builder.frameBody.on("easyticket.undo.add easyticket.undo.restore", function (e) { Easyticket.CodeEditor.setValue(e);});
		//load code when a new url is loaded
		Easyticket.Builder.documentFrame.on("load", function (e) { Easyticket.CodeEditor.setValue();});

		this.isActive = true;
		this.setValue();

		return this.codemirror;
	},

	setValue: function(value) {
		if (this.isActive == true)
		{
			var scrollInfo = this.codemirror.getScrollInfo();
			this.codemirror.setValue(Easyticket.Builder.getHtml());
			this.codemirror.scrollTo(scrollInfo.left, scrollInfo.top);
		}
	},

	destroy: function(element) {
		/*
		//save memory by destroying but lose scroll on editor toggle
		this.codemirror.toTextArea();
		this.codemirror = false;
		*/ 
		this.isActive = false;
	},

	toggle: function() {
		if (this.isActive != true)
		{
			this.isActive = true;
			return this.init();
		}
		this.isActive = false;
		this.destroy();
	}
}
