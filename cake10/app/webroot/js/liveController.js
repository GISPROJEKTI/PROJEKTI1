(function() {
    var LiveController = Spine.Controller.create({
        initLive: function(callback) {
            
        },
        updateValue: function(evt) {
            var target = $(evt.currentTarget),
                name = target.attr("name"),
                value;

            if (target.is("input[type='checkbox']")) {
                value = target.is(":checked");
            } else if (target.is("input[type='radio']")) {
                value = $("input[name='" + name + "']:checked", this.el).val();
            } else if (target.is("select")) {
                value = $("option:selected", target).val();
            } else {
                value = target.val();
            }

            
        },
        ass: function() {
            console.info("ass");
        }
    });
    this.LiveController = LiveController;
}).call(this);