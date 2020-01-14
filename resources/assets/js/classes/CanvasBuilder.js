export default class CanvasBuilder {

    constructor(){

        this.canvas = document.createElement("CANVAS");
        this.context = this.canvas.getContext('2d');

    }

    setAlpha(alpha) {

        this.context.globalAlpha = alpha;

    };

    makeImage(icon, color) {

        color = `#${color}`;
        let reference = this;

        function drawRectangle(color) {
            reference.context.fillStyle = color;
            reference.context.fillRect(0, 0, 300, 230);
        }

        function drawTriangle(color) {
            reference.context.fillStyle = color;
            let region = new Path2D();
            region.moveTo(150, 350);
            region.lineTo(90, 230);
            region.lineTo(210, 230);
            region.closePath();
            reference.context.fill(region);
        }

        function drawText(icon) {
            reference.context.font = '150px Arial';
            reference.context.textAlign = "center";
            reference.context.fillStyle = '#fff';
            reference.context.fillText(icon, 150, 170, 300);
        }

        drawRectangle(color);
        drawTriangle(color);
        drawText(icon);

        let image = this.canvas.toDataURL("image/png");

        this.reset();

        return image;

    };

    reset() {

        this.context.clearRect(0, 0, this.canvas.width, this.canvas.height);

    };

    initialize() {

        this.canvas.height = 350;
        this.canvas.width = 300;

    };

}