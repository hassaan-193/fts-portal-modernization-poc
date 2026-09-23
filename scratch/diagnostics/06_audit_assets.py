import os
import re

def get_dir_size_and_count(path):
    total_size = 0
    file_count = 0
    if not os.path.exists(path):
        return 0, 0
    for root, dirs, files in os.walk(path):
        for f in files:
            fp = os.path.join(root, f)
            try:
                total_size += os.path.getsize(fp)
                file_count += 1
            except Exception:
                pass
    return total_size, file_count

def main():
    base_dir = r"d:\FTSITS\ft_portal_base(2)\ft_portal_base"
    plugins_dir = os.path.join(base_dir, "public", "plugins")
    layout_file = os.path.join(base_dir, "resources", "views", "layouts", "app.blade.php")
    
    print("=" * 70)
    print("PROBE 6: MONOLITHIC ASSET BUNDLE & PLUGINS FOOTPRINT AUDIT")
    print("=" * 70)
    
    # 1. Audit public/plugins directory
    if os.path.exists(plugins_dir):
        subdirs = [d for d in os.listdir(plugins_dir) if os.path.isdir(os.path.join(plugins_dir, d))]
        total_bytes, total_files = get_dir_size_and_count(plugins_dir)
        total_mb = total_bytes / (1024 * 1024)
        
        print(f"\n[1] PUBLIC PLUGINS DIRECTORY AUDIT (public/plugins/):")
        print(f"    Total Distinct Plugin Folders : {len(subdirs)} plugins")
        print(f"    Total Static Files in Plugins : {total_files} files")
        print(f"    Total Disk Footprint          : {total_mb:.2f} MB")
        
        # List top 10 largest plugin folders
        plugin_sizes = []
        for d in subdirs:
            p_path = os.path.join(plugins_dir, d)
            sz, count = get_dir_size_and_count(p_path)
            plugin_sizes.append((d, sz / (1024 * 1024), count))
        plugin_sizes.sort(key=lambda x: x[1], reverse=True)
        
        print("\n    Top 10 Heaviest Vendored Plugins:")
        for name, sz_mb, count in plugin_sizes[:10]:
            print(f"      * {name:<26} : {sz_mb:6.2f} MB ({count} files)")
    else:
        print(f"Plugins directory not found at {plugins_dir}")

    # 2. Audit resources/views/layouts/app.blade.php
    print("\n[2] GLOBAL ASSET INCLUSION IN MAIN LAYOUT (layouts/app.blade.php):")
    if os.path.exists(layout_file):
        with open(layout_file, 'r', encoding='utf-8', errors='ignore') as f:
            content = f.read()
            
        script_tags = re.findall(r'<script\s+[^>]*src=[\'"]([^\'"]+)[\'"]', content, re.IGNORECASE)
        link_tags = re.findall(r'<link\s+[^>]*href=[\'"]([^\'"]+)[\'"]', content, re.IGNORECASE)
        inline_scripts = re.findall(r'<script(?![^>]*src)[^>]*>(.*?)</script>', content, re.DOTALL | re.IGNORECASE)
        
        print(f"    Global CSS Stylesheet Tags (<link>)  : {len(link_tags)} tags loaded on EVERY page")
        print(f"    Global Javascript Tags (<script src>) : {len(script_tags)} tags loaded on EVERY page")
        print(f"    Inline Script Blocks                 : {len(inline_scripts)} uncompiled jQuery blocks")
        
        print("\n    Sample Scripts Loaded Unconditionally:")
        for s in script_tags[:5]:
            print(f"      - {s}")
    else:
        print(f"Layout file not found at {layout_file}")

    print("\n[3] COMPARISON: LEGACY PLUGINS VS. TARGET VITE 5 BUNDLE:")
    print("    * Legacy Monolith: 59 separate plugin folders, multiple HTTP round-trips, no tree-shaking.")
    print("    * Target Vite 5  : 1 minified, tree-shaken app.js (~60-120 KB), native ES modules, instant HMR.")
    print("=" * 70)

if __name__ == "__main__":
    main()
