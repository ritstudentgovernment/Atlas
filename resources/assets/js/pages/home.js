window.CanvasBuilder = function(){

    let canvas = document.createElement("CANVAS");
    let context = canvas.getContext('2d');

    this.makeImage = function(icon, color){

        function drawRectangle(color){
            context.fillStyle = color;
            context.fillRect(0, 0, 300, 230);
        }

        function drawTriangle(color){
            context.fillStyle = color;
            context.beginPath();
            context.moveTo(150, 350);
            context.lineTo(90, 230);
            context.lineTo(210, 230);
            context.fill();
        }

        function drawText(icon) {
            context.font = '150px Arial';
            context.textAlign = "center";
            context.fillStyle = '#fff';
            context.fillText(icon, 150, 170, 300);
        }

        drawRectangle(color);
        drawTriangle(color);
        drawText(icon);

        let image = canvas.toDataURL("image/png");

        this.reset();

        return image;

    };

    this.reset = function(){

        context.clearRect(0, 0, canvas.width, canvas.height);

    };

    this.initialize = function(){

        canvas.height = 350;
        canvas.width = 300;

    };

};

