<tr>
    <td style="padding: {{conf-paddings}};">
        <div style="width: {{conf-width}}; margin: 0 auto;">
            <!--[if mso]>
            <v:roundrect
                xmlns:v="urn:schemas-microsoft-com:vml"
                xmlns:w="urn:schemas-microsoft-com:office:word"
                href="{{conf-link}}"
                style="height: 50px; v-text-anchor: middle; width: {{conf-width}};"
                arcsize="0%"
                stroke="f"
                fillcolor="#2181F8"
                >
                <w:anchorlock></w:anchorlock>
                <center style="color: #ffffff; font-family: Arial; font-size: 14px; letter-spacing: 1.05px; font-weight: regular;">
                {{conf-text}}
                </center>
            </v:roundrect>
            <![endif]-->
            <![if !mso]>
            <table style="width: 100%; padding: 0; margin: 0;" cellspacing="0" cellpadding="0">
                <tr>
                    <td style="display: block; font-size: 0;" align="center" height="50" bgcolor="#2181F8">
                        <a style="width: 100%; 
                                    padding-left: 1px; 
                                    font-family: Arial; 
                                    font-size: {{conf-fontsize}}; 
                                    letter-spacing: 0.28px; 
                                    text-decoration: none; 
                                    height: 18px;
                                    padding-top: 16px;
                                    padding-bottom: 16px;
                                    display: inline-block;"
                            href="{{conf-link}}"
                            target="_blank">
                        <span style="color: #ffffff;">{{conf-text}}</span>
                        </a>
                    </td>
                </tr>
            </table>
            <![endif]>
        </div>
    </td>
</tr>