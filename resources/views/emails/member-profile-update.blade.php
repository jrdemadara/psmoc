<p>Hello {{ $data['name'] }},</p>

<p>We’ve received your request to update your <strong>PSMOC</strong> membership profile.</p>

<p>To proceed, please click the button below. This link is valid for one-time use and will expire in 24 hours for security purposes.</p>

<br>
<p style="text-align: center;">
    <a href="{{ $data['url'] }}"
       style="background-color: #FF003F; color: #ffffff; padding: 12px 20px; text-decoration: none; border-radius: 6px; font-weight: bold;">
       Update My Profile
    </a>
</p>
<br>
<p>If you didn’t request this update, you can safely ignore this email — your profile will remain unchanged.</p>

<p>For any questions or concerns, feel free to contact us at
    <a href="mailto:contact@psmoc.org">contact@psmoc.org</a>.
</p>

<p>Best regards,<br>The PSMOC Team</p>
