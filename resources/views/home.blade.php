@extends('layouts.main')

@section('container')
    <div style="background-color: #121212; color: #ffffff;">
        <div class="home-image-container">
            <div class="home-dark-overlay"></div>
            <img src="{{ asset('img/home1.png') }}" alt="Forest Fire" class="home-image">
            <div class="home-overlay">
                <h1 class="home-title">Welcome to Forest Fire Prediction</h1>
                <p class="home-text">Empowering communities and protecting nature through advanced fire prediction technology. Join us in safeguarding our forests and wildlife.</p>            
            </div>
        </div>
    <footer class="home-footer">
        <div class="footer-left">
            <div class="logo-container">
                <img src="{{ asset('img/TelU.png') }}" alt="Logo" class="footer-logo">
            </div>
            <div class="logo-container">
                <img src="{{ asset('img/BMKG.png') }}" alt="Second Logo" class="footer-logo">
            </div>
        </div>
        <div class="footer-right">
            <p><strong>Contact Us:</strong> <br> Forest Fire Prediction <br> Telp: (021) 220220 <br> Email: forestfirepredictionelm@gmail.com</p>
        </div>
    </footer>
        
        <div class="website-discussion" style="margin: 40px 20px 110px 20px; background-color: #121212; color: #ffffff;">
            <h2>About This Website</h2>
            <p>This website is dedicated to predicting forest fires using advanced technological methods and data analysis. Our aim is to help communities and environmental agencies to proactively manage and mitigate the risks associated with forest fires.</p>
            <p>The components used in our prediction model include:</p>
            <div class="components-container" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
                <div class="component" style="display: flex; flex-direction: column; justify-content: space-between; background-color: #1e1e1e; padding: 20px; border-radius: 8px;">
                    <div>
                        <h3>FFMC</h3>
                        <p><strong>Fine Fuel Moisture Code (FFMC):</strong> The Fine fuel moisture code (FFMC) is one of the three fuel moisture code components of the Canadian forest fire weather index (FWI) system. The FFMC represents the moisture content of litter and other cured fine fuels in a forest stand, in a layer of dry weight about 0.25 kg/m2, and assesses the relative ease of ignition and the flammability of fine fuels at mid-afternoon. It requires temperature, relative air humidity, wind speed and precipitation (at noon) as input data (Van Wagner 1987).</p>
                    </div>
                    <a href="https://wikifire.wsl.ch/tiki-index91f7.html?page=Fine+fuel+moisture+code" class="btn btn-primary" target="_blank">Read More</a>
                </div>
                <div class="component" style="display: flex; flex-direction: column; justify-content: space-between; background-color: #1e1e1e; padding: 20px; border-radius: 8px;">
                    <div>
                        <h3>DMC</h3>
                        <p><strong>Duff Moisture Code (DMC):</strong> The Duff moisture code (DMC) is one of the three fuel moisture code components of the Canadian forest fire weather index (FWI) system. The DMC represents the moisture content of loosely compacted, decomposing organic matter weighing about 5 kg/m2 when dry. It assesses fuel consumption in moderate duff layers and medium-size woody material at mid-afternoon. It requires temperature, relative air humidity, precipitation (at noon), and current month (in order to take daylength into account) as input data (Van Wagner 1987).</p>
                    </div>
                    <a href="https://wikifire.wsl.ch/tiki-index9436.html?page=Duff+moisture+code" class="btn btn-primary" target="_blank">Read More</a>
                </div>
                <div class="component" style="display: flex; flex-direction: column; justify-content: space-between; background-color: #1e1e1e; padding: 20px; border-radius: 8px;">
                    <div>
                        <h3>DC</h3>
                        <p><strong>Drought Code (DC):</strong> The Drought code (DC) is one of the three fuel moisture code components of the Canadian forest fire weather index (FWI) system. The DC represents the moisture content of deep compact layer of organic matter weighing about 25 kg/m2 when dry. It assesses the effects of seasonal drought on deep duff layers and heavy fuels. It requires noon temperature, precipitation and current month (in order to take daylength into account) as input data (Van Wagner 1987).</p>
                    </div>
                    <a href="https://wikifire.wsl.ch/tiki-indexd5c6.html?page=Drought+code" class="btn btn-primary" target="_blank">Read More</a>
                </div>
                <div class="component" style="display: flex; flex-direction: column; justify-content: space-between; background-color: #1e1e1e; padding: 20px; border-radius: 8px;">
                    <div>
                        <h3>ISI</h3>
                        <p><strong>Initial Spread Index (ISI):</strong> The Initial spread index (ISI) is one of the two intermediate indices required for calculating the Canadian fire weather index (FWI). The ISI represents the rate of fire spread without the influence of variable quantities of fuel. It is a combination of the effects of wind speed and fine fuel moisture content on fire spread, and thus requires windspeed and the FFMC as input variables (Van Wagner 1987).</p>
                    </div>
                    <a href="https://wikifire.wsl.ch/tiki-index4de6.html?page=Initial+spread+index" class="btn btn-primary" target="_blank">Read More</a>
                </div>
                <div class="component" style="display: flex; flex-direction: column; justify-content: space-between; background-color: #1e1e1e; padding: 20px; border-radius: 8px;">
                    <div>
                        <h3>BUI</h3>
                        <p><strong>Build-Up Index (BUI):</strong> The Buildup index (BUI) is one of the two intermediate indices required for calculating the Canadian fire weather index (FWI). The BUI represents the total amount of fuel available to the spreading fire. It is a combination of the DMC and the DC (Van Wagner 1987). The BUI is affected mainly by the DMC. The DC only has a limited and variable weight in the equation (Van Wagner 1987).</p>
                    </div>
                    <a href="https://wikifire.wsl.ch/tiki-index8720.html?page=Buildup+index" class="btn btn-primary" target="_blank">Read More</a>
                </div>
                <div class="component" style="display: flex; flex-direction: column; justify-content: space-between; background-color: #1e1e1e; padding: 20px; border-radius: 8px;">
                    <div>
                        <h3>FWI</h3>
                        <p><strong>Fire Weather Index (FWI):</strong> The Fire weather index (FWI) is the final index of the FWI system. It consists of the combination of the ISI and BUI and is a measure of fire intensity in the form of energy output rate per unit length of fire front. Nevertheless, the FWI is appropriated for predicting various aspects of fire activity as it combines all influencing factors in one number (Van Wagner 1987).</p>
                    </div>
                    <a href="https://wikifire.wsl.ch/tiki-index259b.html?page=Fire+weather+index" class="btn btn-primary" target="_blank">Read More</a>
                </div>
            </div>
            <!-- Tambahkan chart di sini -->
            <div style="margin-top: 40px;">
                <h2>Flowchart FWI</h2>
                <img src="{{ asset('img/Flowchart FWI-3.png') }}" alt="Flowchart FWI" style="max-width: 100%; height: auto;">
            </div>
        </div>
    </div>
@endsection
