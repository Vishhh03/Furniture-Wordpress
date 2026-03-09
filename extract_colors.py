import sys
import os
from PIL import Image
import colorgram

image_paths = [
    r"C:\Users\visha\Desktop\Furniture\Rest _n_ Revel\ig posts\Frame 69.png",
    r"C:\Users\visha\Desktop\Furniture\Rest _n_ Revel\ig posts\Frame 70.png",
    r"C:\Users\visha\Desktop\Furniture\Rest _n_ Revel\ig posts\Frame 71.png",
    r"C:\Users\visha\Desktop\Furniture\Rest _n_ Revel\ig posts\Slide 16_9 - 19.png",
]

all_colors = []

for p in image_paths:
    if os.path.exists(p):
        print(f"Analyzing {os.path.basename(p)}...")
        colors = colorgram.extract(p, 5)
        for c in colors:
            hex_color = '#%02x%02x%02x' % (c.rgb.r, c.rgb.g, c.rgb.b)
            print(f" - {hex_color} ({c.proportion*100:.2f}%)")
            all_colors.append((c.proportion, hex_color))

print("\nTop colors:")
all_colors.sort(reverse=True)
unique_colors = []
for p, c in all_colors:
    if c not in unique_colors:
        unique_colors.append(c)
        print(c)
