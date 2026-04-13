import re

with open('member/member_dashboard.php', 'r', encoding='utf-8') as f:
    content = f.read()

# Find the closing of the valid two-column row section (ends with </div>\n\n</div>)
# and the start of the real MODALS comment
# The structure after clean section is: </div>\n\n</div>\n\n<!-- MODALS -->\n
# But there's junk between the first </div> close and the second <!-- MODALS -->

# Strategy: Find the FIRST occurrence of "<!-- PROFILE INFO MODAL -->"
# and keep everything from "<!-- MODALS -->" just before that

# Split at the second (real) <!-- MODALS --> occurrence
parts = content.split('<!-- MODALS -->')
print(f"Found {len(parts)} parts split by '<!-- MODALS -->'")

if len(parts) >= 3:
    # parts[0] = everything before first <!-- MODALS -->
    # parts[1] = junk between the two <!-- MODALS -->
    # parts[2] = real modal content (profile info modal onward)
    clean = parts[0] + '<!-- MODALS -->\n' + parts[2]
elif len(parts) == 2:
    clean = content
    print("Only one MODALS comment found, nothing to clean")
else:
    print("No MODALS comment found!")
    clean = content

with open('member/member_dashboard.php', 'w', encoding='utf-8') as f:
    f.write(clean)

print(f"Done! Original length: {len(content)}, New length: {len(clean)}")
print(f"Difference: {len(content) - len(clean)} chars removed")
