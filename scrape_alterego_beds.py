"""
AlterEgo Beds - Full Image Scraper
===================================
Downloads all bed product images (including variant-specific images)
at the highest possible resolution from alteregobeds.com (Shopify store).

Images are saved to:  ./alterego_beds_images/<product-name>/<image>.jpg

Usage:
    python scrape_alterego_beds.py

Requirements:
    pip install requests
"""

import os
import re
import json
import time
import hashlib
import requests
from urllib.parse import urlparse, urlencode, urlunparse, parse_qs, urljoin

BASE_URL = "https://alteregobeds.com"
OUTPUT_DIR = os.path.join(os.path.dirname(__file__), "alterego_beds_images")
HEADERS = {
    "User-Agent": (
        "Mozilla/5.0 (Windows NT 10.0; Win64; x64) "
        "AppleWebKit/537.36 (KHTML, like Gecko) "
        "Chrome/122.0.0.0 Safari/537.36"
    ),
    "Accept": "application/json, text/html, */*",
}

# ── Helpers ────────────────────────────────────────────────────────────────────

def sanitize(name: str) -> str:
    """Make a string safe for use as a directory / file name."""
    return re.sub(r'[\\/*?:"<>|]', "_", name).strip()


def max_quality_url(url: str) -> str:
    """
    Shopify CDN images accept a ?width= query param.
    Remove it (or bump it to 5760 – the largest Shopify supports)
    to get the highest-quality version of the image.
    """
    parsed = urlparse(url)
    # Strip width, w, height, h, crop, pad params to get original file
    clean_qs = {}
    for k, v in parse_qs(parsed.query).items():
        if k not in ("width", "w", "height", "h", "crop", "pad"):
            clean_qs[k] = v[0]

    new_query = urlencode(clean_qs)
    return urlunparse(parsed._replace(query=new_query))


def download(url: str, dest_path: str, session: requests.Session) -> bool:
    """Download a single file; return True on success."""
    if os.path.exists(dest_path):
        print(f"  [skip] already exists: {os.path.basename(dest_path)}")
        return True
    try:
        r = session.get(url, headers=HEADERS, timeout=60, stream=True)
        r.raise_for_status()
        os.makedirs(os.path.dirname(dest_path), exist_ok=True)
        with open(dest_path, "wb") as f:
            for chunk in r.iter_content(chunk_size=1024 * 64):
                f.write(chunk)
        print(f"  [ok] {os.path.basename(dest_path)}")
        return True
    except Exception as e:
        print(f"  [error] {url}  →  {e}")
        return False


# ── Shopify API calls ──────────────────────────────────────────────────────────

def get_all_products(session: requests.Session) -> list:
    """
    Use Shopify's public /products.json endpoint (no API key needed).
    Paginate using the page_info cursor approach (limit=250 covers most stores).
    """
    products = []
    url = f"{BASE_URL}/products.json?limit=250"
    while url:
        r = session.get(url, headers=HEADERS, timeout=30)
        r.raise_for_status()
        data = r.json()
        batch = data.get("products", [])
        products.extend(batch)
        print(f"  Fetched {len(batch)} products (total so far: {len(products)})")

        # Shopify uses Link header for cursor-based pagination
        link_header = r.headers.get("Link", "")
        next_url = None
        for part in link_header.split(","):
            part = part.strip()
            if 'rel="next"' in part:
                next_url = part.split(";")[0].strip().strip("<>")
        url = next_url
        if url:
            time.sleep(0.5)

    return products


def get_product_images_from_api(product: dict) -> list:
    """
    Extract every unique image URL from a Shopify product JSON object,
    including images that are only referenced on specific variants.
    Returns a list of (url, label) tuples.
    """
    seen = set()
    results = []

    def add(url, label):
        hq = max_quality_url(url)
        if hq not in seen:
            seen.add(hq)
            results.append((hq, label))

    # Product-level images
    for img in product.get("images", []):
        src = img.get("src", "")
        if src:
            add(src, f"product_img_{img.get('position', 0):03d}")

    # Variant featured_image (sometimes unique images per variant)
    for variant in product.get("variants", []):
        fi = variant.get("featured_image")
        if fi and fi.get("src"):
            label_parts = [variant.get("option1", ""), variant.get("option2", ""), variant.get("option3", "")]
            label = "_".join(p for p in label_parts if p)
            add(fi["src"], f"variant_{sanitize(label)}")

    return results


def get_product_detail_images(handle: str, session: requests.Session) -> list:
    """
    Fetch the single-product JSON endpoint for even more detail,
    e.g. metafield images or additional media not in /products.json.
    """
    url = f"{BASE_URL}/products/{handle}.json"
    try:
        r = session.get(url, headers=HEADERS, timeout=30)
        r.raise_for_status()
        product = r.json().get("product", {})
        return get_product_images_from_api(product)
    except Exception as e:
        print(f"  [warn] Could not fetch product detail for {handle}: {e}")
        return []


# ── Main ───────────────────────────────────────────────────────────────────────

def main():
    session = requests.Session()
    session.headers.update(HEADERS)

    print("=" * 60)
    print("AlterEgo Beds — Image Scraper")
    print("=" * 60)
    print(f"Output directory: {OUTPUT_DIR}\n")

    # 1. Get full product catalog
    print("[1/3] Fetching product catalog from Shopify API...")
    products = get_all_products(session)
    print(f"  Total products found: {len(products)}\n")

    if not products:
        print("No products found. Exiting.")
        return

    # 2. For each product, gather all image URLs
    print("[2/3] Collecting image URLs for each product...\n")
    download_queue = []  # list of (local_path, url)
    manifest = {}

    for product in products:
        title = product.get("title", "Unknown")
        handle = product.get("handle", "unknown")
        print(f"  Product: {title}")

        product_dir = os.path.join(OUTPUT_DIR, sanitize(title))

        # Get images from listing endpoint
        images_from_list = get_product_images_from_api(product)

        # Also fetch from per-product endpoint (may have extras)
        images_from_detail = get_product_detail_images(handle, session)

        # Merge, deduplicating by URL
        all_imgs = {url: label for url, label in images_from_list}
        for url, label in images_from_detail:
            if url not in all_imgs:
                all_imgs[url] = label

        print(f"    → {len(all_imgs)} unique images")

        manifest[title] = []
        for idx, (url, label) in enumerate(all_imgs.items(), start=1):
            ext = os.path.splitext(urlparse(url).path)[-1] or ".jpg"
            filename = f"{idx:03d}_{label}{ext}"
            local_path = os.path.join(product_dir, filename)
            download_queue.append((local_path, url))
            manifest[title].append({"file": filename, "url": url, "label": label})

        time.sleep(0.3)

    # 3. Download everything
    print(f"\n[3/3] Downloading {len(download_queue)} images...\n")
    success = 0
    for local_path, url in download_queue:
        product_name = os.path.basename(os.path.dirname(local_path))
        print(f"[{product_name}]")
        ok = download(url, local_path, session)
        if ok:
            success += 1
        time.sleep(0.1)  # small delay to be polite

    # Save manifest JSON
    manifest_path = os.path.join(OUTPUT_DIR, "manifest.json")
    with open(manifest_path, "w", encoding="utf-8") as f:
        json.dump(manifest, f, indent=2, ensure_ascii=False)

    print("\n" + "=" * 60)
    print(f"Done! {success}/{len(download_queue)} images downloaded.")
    print(f"Manifest saved to: {manifest_path}")
    print(f"Images saved to:   {OUTPUT_DIR}")
    print("=" * 60)


if __name__ == "__main__":
    main()
