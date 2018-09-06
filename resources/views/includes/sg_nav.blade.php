<style>.dropbtn,.dropbtn-mobile{background-color:#f70;color:#fff;font-size:12px;border:none;outline:0}.dropbtn,.dropbtn-mobile,.dropdown-left a{font-weight:700;cursor:pointer}#sg-global-nav{overflow:hidden;height:30px;padding:0}.dropbtn{position:relative;padding-right:20px;padding-bottom:5px;padding-top:5px}.dropbtn-mobile{padding-right:5%;height:30px}.dropdown-left{text-align:left;position:fixed;display:inline-block;width:50%;z-index:99;height:30px;padding-left:5%;font-family:Montserrat;line-height:27px}.dropdown-right-main,.dropdown-right-mobile{z-index:99;height:30px;font-family:Montserrat;text-align:right}.dropdown-right-mobile{display:none;position:fixed;width:50%;margin-left:50%}.dropdown-content,.dropdown-content-mobile{position:absolute;min-width:160px;box-shadow:0 8px 16px 0 rgba(0,0,0,.2);z-index:1;text-align:left;font-size:12px}.dropdown-content{display:none;right:0;background-color:#f9f9f9;border-top:8px;border-top-style:solid;border-top-color:#f70;margin-top:5px;font-family:Montserrat}.dropdown-content a,.dropdown-content-mobile a,.dropdown-content-mobile p{display:block;font-family:Montserrat;text-decoration:none}.dropdown-content a{color:#000;padding:12px 16px}.dropdown-content-mobile p{color:#000;padding:8px 12px;margin-bottom:0;background-color:#d3d3d3}.dropdown-content a:hover{color:grey}.dropdown-content-mobile a{color:#000;padding:0 8px}.dropdown-content-mobile a:hover{color:grey}.dropdown-content-mobile{display:none;background-color:#f9f9f9;border-top-style:solid;border-top-color:#f70;font-family:Montserrat;right:10px;padding-bottom:10px}.dropdown-left a{background-color:#f70;color:#fff;font-size:12px;border:none;font-family:Montserrat;height:20px;text-align:center;margin-top:20px}.dropdown-left a:hover{text-decoration:none}.header-top{height:30px;background-color:#f70;z-index:1001;position:fixed;top:0;left:0;right:0}body,html{position:relative}.show{display:block}@media screen and (max-width:992px){.dropdown-right-main{display:none}.dropdown-right-mobile{display:inline-block}}</style>

<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet">

<nav id="sg-global-nav">
    <div class="header-top">
        <div class="dropdown-left">
            <a href="https://sg.rit.edu/">RIT Student Government</a>
        </div>
        <div class="dropdown-right-main">
            <button id="1" onmouseover="openDropdown(this.id)" onmouseout="closeDropdown(this.id)" class="dropbtn">
                About
                <i class="material-icons" style="font-size:12px;font-weight: bold;">keyboard_arrow_down</i>
                <div id="myDropdown1" class="dropdown-content">
                    <a href="https://sg.rit.edu/about/leadership/">Leadership</a>
                    <a href="https://sg.rit.edu/about/">Structure</a>
                    <a href="https://sg.rit.edu/about/mission-and-vision/">Mission and Vision</a>
                    <a href="https://sg.rit.edu/about/committees/">Committees</a>
                    <a href="https://sg.rit.edu/about/msos/">MSO's</a>
                    <a href="https://sg.rit.edu/awards/">Awards</a>
                </div>
            </button>
            <button id="2" onmouseover="openDropdown(this.id)" onmouseout="closeDropdown(this.id)" class="dropbtn">
                Services
                <i class="material-icons" style="font-size:12px;font-weight: bold;">keyboard_arrow_down</i>
                <div id="myDropdown2" class="dropdown-content">
                    <a href="https://sg.rit.edu/services/">Services Overview</a>
                    <a href="https://pawprints.rit.edu/">Paw Prints</a>
                    <a href="https://bikeshare.rit.edu/">Bike Share</a>
                    <a href="https://sg.rit.edu/legalaid/">Legal Aid</a>
                    <a href="https://ritpedia.rit.edu/Main_Page">RITpedia</a>
                    <a href="https://openevals.rit.edu/">Open Evals</a>
                    <a href="https://sites.sg.rit.edu/">SG Sites</a>
                    <a href="https://sg.rit.edu/finance/">Finance</a>
                    <a href="https://sg.rit.edu/breakbus/">Bike Share</a>
                </div>
            </button>
            <button id="3" onmouseover="openDropdown(this.id)" onmouseout="closeDropdown(this.id)" class="dropbtn">
                Resources
                <i class="material-icons" style="font-size:12px;font-weight: bold;">keyboard_arrow_down</i>
                <div id="myDropdown3" class="dropdown-content">
                    <a href="https://sg.rit.edu/sgupdates/">SG Updates</a>
                    <a href="https://sg.rit.edu/resources/">Resourse List</a>
                    <a href="https://sg.rit.edu/resources/sg-logos/">SG Logos</a>
                </div>
            </button>
            <button id="4" onmouseover="openDropdown(this.id)" onmouseout="closeDropdown(this.id)" class="dropbtn">Get
                Involved
                <i class="material-icons" style="font-size:12px;font-weight: bold;">keyboard_arrow_down</i>
                <div id="myDropdown4" class="dropdown-content">
                    <a href="https://sg.rit.edu/spirit/">Project SpiRIT</a>
                    <a href="https://sg.rit.edu/get-involved/events-calendar/">Event Calendar</a>
                    <a href="https://sg.rit.edu/get-involved/">Opportunities</a>
                </div>
            </button>
        </div>
        <div class="dropdown-right-mobile">
            <button id="10" onmouseover="openDropdown(this.id)" onmouseout="closeDropdown(this.id)"
                    class="dropbtn-mobile">
                <i class="material-icons">dehaze</i>
                <div id="myDropdown10" class="dropdown-content-mobile">

                    <p>About</p>
                    <a href="https://sg.rit.edu/about/leadership/">Leadership</a>
                    <a href="https://sg.rit.edu/about/">Structure</a>
                    <a href="https://sg.rit.edu/about/mission-and-vision/">Mission and Vision</a>
                    <a href="https://sg.rit.edu/about/committees/">Committees</a>
                    <a href="https://sg.rit.edu/about/msos/">MSO's</a>
                    <a href="https://sg.rit.edu/awards/">Awards</a>

                    <p>Services</p>
                    <a href="https://sg.rit.edu/services/">Overview</a>
                    <a href="https://pawprints.rit.edu/">Paw Prints</a>
                    <a href="https://bikeshare.rit.edu/">Bike Share</a>
                    <a href="https://sg.rit.edu/legalaid/">Legal Aid</a>
                    <a href="https://ritpedia.rit.edu/Main_Page">RITpedia</a>
                    <a href="https://openevals.rit.edu/">Open Evals</a>
                    <a href="https://sites.sg.rit.edu/">SG Sites</a>
                    <a href="https://sg.rit.edu/finance/">Finance</a>
                    <a href="https://sg.rit.edu/breakbus/">Bike Share</a>

                    <p>Resources</p>
                    <a href="https://sg.rit.edu/sgupdates/">SG Updates</a>
                    <a href="https://sg.rit.edu/resources/">Resourse List</a>
                    <a href="https://sg.rit.edu/resources/sg-logos/">SG Logos</a>

                    <p>Get Involved</p>
                    <a href="https://sg.rit.edu/spirit/">Project SpiRIT</a>
                    <a href="https://sg.rit.edu/get-involved/events-calendar/">Event Calendar</a>
                    <a href="https://sg.rit.edu/get-involved/">Opportunities</a>

                </div>
            </button>
        </div>
    </div>
</nav>

<script>
    function openDropdown(id) {
//        console.log(document.getElementById(id));
        var dropdowns = document.getElementsByClassName("dropdown-content");
        for (i = 0; i != dropdowns.length; i++) {
            var openDropdown = dropdowns[i];
            if (openDropdown.classList.contains('show')) {
                openDropdown.classList.remove('show');
            }
        }
        document.getElementById("myDropdown" + id).classList.toggle("show");
    }


    function closeDropdown(id) {
//        console.log(document.getElementById(id)+"close");
        document.getElementById("myDropdown" + id).classList.remove("show");

    }


    window.onclick = function (event) {
        if (!event.target.matches('.dropbtn')) {
            var dropdowns = document.getElementsByClassName("dropdown-content");
            var i;
            for (i = 0; i != dropdowns.length; i++) {
                var openDropdown = dropdowns[i];
                if (openDropdown.classList.contains('show')) {
                    openDropdown.classList.remove('show');
                }
            }
        }
    }
</script>