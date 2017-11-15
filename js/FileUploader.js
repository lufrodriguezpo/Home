(function() {        
    // 1
    function FileUploader(selector) {
        if (undefined !== selector) {
            this.init(selector);
        }
    }
     
    // 2
    FileUploader.prototype.init = function(selector) {
        if (undefined !== this.$el) {
            this.unsuscribe();
        }
         
        this.$el = document.querySelector(selector);
        this.$fileInput = this.$el.querySelector('input');
        this.$img = this.$el.querySelector('img');
         
        this.suscribe();
    }
 
    // 3
    FileUploader.prototype.suscribe = function() {
        this.$fileInput.addEventListener('change', _handleInputChange.bind(this));
        this.$img.addEventListener('load', _handleImageLoaded.bind(this));
    }
     
    // 4
    FileUploader.prototype.unsuscribe = function() {
        this.$fileInput.removeEventListener('change', _handleInputChange.bind(this));
        this.$img.removeEventListener('load', _handleImageLoaded.bind(this));
    }
 
    // 5
    function _handleImageLoaded() {
        if (!this.$img.classList.contains('loaded')) {
            this.$img.classList.add('loaded');
        }
    }
 
    // 6
    function _handleInputChange(e) {
        // 6.1
        var file = (undefined !== e)
            ? e.target.files[0]
            : this.$img.files[0];
 
        var pattern = /image-*/;
        var reader = new FileReader();
 
        // 6.2
        if (!file.type.match(pattern)) {
            alert('invalid format');
            return;
        }
 
        if (this.$el.classList.contains('loaded')) {
            this.$el.classList.remove('loaded');
        }
 
        // 6.3
        reader.onload = _handleReaderLoaded.bind(this);
        reader.readAsDataURL(file);
    }
 
    // 7
    function _handleReaderLoaded(e) {
        var reader = e.target;
        this.$img.src = reader.result;
        this.$el.classList.add('loaded');
    }
     
    // 8
    window.FileUploader = FileUploader;
     
} ());