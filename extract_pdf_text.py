import sys
try:
    import fitz
except ImportError:
    print("PyMuPDF (fitz) is not installed.")
    sys.exit(1)

pdf_path = r"C:\Users\visha\Desktop\Furniture\Rest _n_ Revel\Rest _n_ Revel Brandbook.pdf"

try:
    doc = fitz.open(pdf_path)
    for i in range(len(doc)):
        page = doc[i]
        text = page.get_text()
        print(f"--- Page {i+1} ---")
        print(text)
    doc.close()
except Exception as e:
    print(f"Error reading PDF: {e}")
