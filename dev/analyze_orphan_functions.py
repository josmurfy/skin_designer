#!/usr/bin/env python3
"""
ShopManager Orphan Function Analyzer
Analyzes all PHP controllers, models, and JS files to find functions never called anywhere.
Target: /home/n7f9655/public_html/phoenixsupplies/admin/shopmanager
"""

import os
import re
import json
from pathlib import Path
from collections import defaultdict

BASE = "/home/n7f9655/public_html/phoenixsupplies/admin"
SCAN_DIRS = {
    "controller": os.path.join(BASE, "controller/shopmanager"),
    "model": os.path.join(BASE, "model/shopmanager"),
    "js": os.path.join(BASE, "view/javascript/shopmanager"),
    "tpl": os.path.join(BASE, "view/template/shopmanager"),
}

# Files/dirs to exclude from scan (old/duplicate files, not active)
EXCLUDE_PATTERNS = [
    "BKP", "backup", ".bak",
    "ebaytemplateOLD", "walmart copy", "ocr copy",
    "product_list copy", "tools copy",
    "fast_addNEW", "fast_add_listNEW", "inventory_listNEW",
    "list_fastNEW", "list_fast_listNEW", "list_fast_listold",
    "ocrNEW", "ocr_image_uploadNEW", "product_formNEW",
    "translateNEW", "toolsNEW",
    "ebay copy",
    "product_search_formOLD",
]

def should_exclude(path):
    for pattern in EXCLUDE_PATTERNS:
        if pattern in path:
            return True
    return False

def get_active_php_files():
    """Get all active PHP files in shopmanager (controller + model)"""
    files = []
    for dir_name in ["controller", "model"]:
        base_dir = SCAN_DIRS[dir_name]
        for root, dirs, filenames in os.walk(base_dir):
            # Skip BKP dirs
            dirs[:] = [d for d in dirs if not should_exclude(d)]
            for fname in filenames:
                if fname.endswith(".php") and not should_exclude(fname):
                    files.append((dir_name, os.path.join(root, fname)))
    return files

def get_active_js_files():
    """Get all active JS files"""
    files = []
    base_dir = SCAN_DIRS["js"]
    for root, dirs, filenames in os.walk(base_dir):
        dirs[:] = [d for d in dirs if not should_exclude(d)]
        for fname in filenames:
            if fname.endswith(".js") and not should_exclude(fname):
                files.append(os.path.join(root, fname))
    return files

def get_active_tpl_files():
    """Get all active TPL templates"""
    files = []
    base_dir = SCAN_DIRS["tpl"]
    for root, dirs, filenames in os.walk(base_dir):
        dirs[:] = [d for d in dirs if not should_exclude(d)]
        for fname in filenames:
            if fname.endswith(".tpl") and not should_exclude(fname):
                files.append(os.path.join(root, fname))
    return files

def extract_php_functions(filepath, file_type):
    """Extract PHP function definitions with line numbers"""
    functions = []
    try:
        with open(filepath, 'r', encoding='utf-8', errors='ignore') as f:
            content = f.read()
        
        # Match public/protected/private function declarations
        pattern = r'(?:public|protected|private)\s+function\s+(\w+)\s*\('
        for m in re.finditer(pattern, content):
            func_name = m.group(1)
            line_no = content[:m.start()].count('\n') + 1
            functions.append({
                "name": func_name,
                "file": filepath,
                "line": line_no,
                "type": file_type
            })
    except Exception as e:
        print(f"  [ERROR reading {filepath}]: {e}")
    return functions

def extract_js_functions(filepath):
    """Extract JS function definitions"""
    functions = []
    try:
        with open(filepath, 'r', encoding='utf-8', errors='ignore') as f:
            content = f.read()
        
        # Named functions: function myFunc(
        pattern1 = r'function\s+(\w+)\s*\('
        # Arrow/var functions: var myFunc = function( OR const myFunc = (
        pattern2 = r'(?:var|let|const)\s+(\w+)\s*=\s*(?:async\s+)?(?:function|\()'
        
        for m in re.finditer(pattern1, content):
            func_name = m.group(1)
            line_no = content[:m.start()].count('\n') + 1
            functions.append({
                "name": func_name,
                "file": filepath,
                "line": line_no,
                "type": "js"
            })
        
        for m in re.finditer(pattern2, content):
            func_name = m.group(1)
            line_no = content[:m.start()].count('\n') + 1
            # Avoid duplicates
            existing = [f["name"] for f in functions if f["file"] == filepath]
            if func_name not in existing:
                functions.append({
                    "name": func_name,
                    "file": filepath,
                    "line": line_no,
                    "type": "js"
                })
    except Exception as e:
        print(f"  [ERROR reading {filepath}]: {e}")
    return functions

def build_full_codebase_text():
    """Build a combined searchable string from all active files"""
    all_content = ""
    
    php_files = get_active_php_files()
    for _, fpath in php_files:
        try:
            with open(fpath, 'r', encoding='utf-8', errors='ignore') as f:
                all_content += f.read() + "\n"
        except:
            pass
    
    js_files = get_active_js_files()
    for fpath in js_files:
        try:
            with open(fpath, 'r', encoding='utf-8', errors='ignore') as f:
                all_content += f.read() + "\n"
        except:
            pass
    
    tpl_files = get_active_tpl_files()
    for fpath in tpl_files:
        try:
            with open(fpath, 'r', encoding='utf-8', errors='ignore') as f:
                all_content += f.read() + "\n"
        except:
            pass
    
    return all_content

def count_usages_in_codebase(func_name, all_content, definition_file):
    """Count how many times a function name is referenced (excluding its own definition)"""
    # Patterns to look for:
    # PHP: ->functionName( | ::functionName( | functionName(
    # JS: functionName( | 'functionName' | route=.../functionName
    
    # Count ALL occurrences
    count = len(re.findall(r'\b' + re.escape(func_name) + r'\b', all_content))
    
    # Subtract the definition itself (usually appears 1-2 times in definition)
    # Count occurrences in the definition file
    try:
        with open(definition_file, 'r', encoding='utf-8', errors='ignore') as f:
            def_content = f.read()
        def_count = len(re.findall(r'\b' + re.escape(func_name) + r'\b', def_content))
    except:
        def_count = 1
    
    return count - def_count

def analyze():
    print("=" * 70)
    print("SHOPMANAGER ORPHAN FUNCTION ANALYZER")
    print("=" * 70)
    
    print("\n[1/4] Building full codebase index...")
    all_content = build_full_codebase_text()
    print(f"  Total codebase size: {len(all_content):,} chars")
    
    print("\n[2/4] Extracting PHP function definitions...")
    php_files = get_active_php_files()
    all_php_functions = []
    for file_type, fpath in php_files:
        funcs = extract_php_functions(fpath, file_type)
        all_php_functions.extend(funcs)
        if funcs:
            rel = fpath.replace(BASE + "/", "")
            print(f"  {rel}: {len(funcs)} functions")
    print(f"  => Total PHP functions: {len(all_php_functions)}")
    
    print("\n[3/4] Extracting JS function definitions...")
    js_files = get_active_js_files()
    all_js_functions = []
    for fpath in js_files:
        funcs = extract_js_functions(fpath)
        all_js_functions.extend(funcs)
        if funcs:
            rel = fpath.replace(BASE + "/", "")
            print(f"  {rel}: {len(funcs)} functions")
    print(f"  => Total JS functions: {len(all_js_functions)}")
    
    print("\n[4/4] Finding orphan functions (never called anywhere)...")
    
    all_functions = all_php_functions + all_js_functions
    
    # Skip these common framework functions that OpenCart calls by convention
    FRAMEWORK_SKIP = {
        'index', 'install', 'uninstall', 'validate', '__construct', '__destruct',
        # OpenCart route-based methods called by URL
    }
    
    orphans_php = []
    orphans_js = []
    
    for func in all_functions:
        name = func["name"]
        
        if name in FRAMEWORK_SKIP:
            continue
        
        usages = count_usages_in_codebase(name, all_content, func["file"])
        func["usages"] = usages
        
        if usages == 0:
            rel_file = func["file"].replace(BASE + "/", "")
            func["rel_file"] = rel_file
            if func["type"] == "js":
                orphans_js.append(func)
            else:
                orphans_php.append(func)
    
    return {
        "all_php": all_php_functions,
        "all_js": all_js_functions,
        "orphans_php": orphans_php,
        "orphans_js": orphans_js,
    }

def format_report(results):
    lines = []
    lines.append("=" * 70)
    lines.append("ORPHAN FUNCTION ANALYSIS REPORT - ShopManager")
    lines.append("Generated: 2026-03-25")
    lines.append("=" * 70)
    
    lines.append(f"\nTotal PHP functions analyzed: {len(results['all_php'])}")
    lines.append(f"Total JS functions analyzed: {len(results['all_js'])}")
    lines.append(f"Orphan PHP functions: {len(results['orphans_php'])}")
    lines.append(f"Orphan JS functions: {len(results['orphans_js'])}")
    
    lines.append("\n" + "=" * 70)
    lines.append("ORPHAN PHP FUNCTIONS (never called anywhere)")
    lines.append("=" * 70)
    
    # Group by file
    by_file = defaultdict(list)
    for f in results['orphans_php']:
        by_file[f["rel_file"]].append(f)
    
    for fpath, funcs in sorted(by_file.items()):
        lines.append(f"\n  FILE: {fpath}")
        for f in sorted(funcs, key=lambda x: x["line"]):
            lines.append(f"    L{f['line']:4d}  {f['name']}()")
    
    lines.append("\n" + "=" * 70)
    lines.append("ORPHAN JS FUNCTIONS (never called anywhere)")
    lines.append("=" * 70)
    
    by_file_js = defaultdict(list)
    for f in results['orphans_js']:
        by_file_js[f["rel_file"]].append(f)
    
    for fpath, funcs in sorted(by_file_js.items()):
        lines.append(f"\n  FILE: {fpath}")
        for f in sorted(funcs, key=lambda x: x["line"]):
            lines.append(f"    L{f['line']:4d}  {f['name']}()")
    
    return "\n".join(lines)

if __name__ == "__main__":
    results = analyze()
    report = format_report(results)
    print("\n\n" + report)
    
    # Save JSON for further processing
    output_json = "/home/n7f9655/public_html/phoenixsupplies/dev/orphan_functions.json"
    os.makedirs(os.path.dirname(output_json), exist_ok=True)
    with open(output_json, 'w') as f:
        json.dump(results, f, indent=2, default=str)
    print(f"\n\nJSON saved to: {output_json}")
    
    output_report = "/home/n7f9655/public_html/phoenixsupplies/dev/orphan_functions_report.txt"
    with open(output_report, 'w') as f:
        f.write(report)
    print(f"Report saved to: {output_report}")
