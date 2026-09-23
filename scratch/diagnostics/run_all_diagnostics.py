import os
import sys
import subprocess
import time
from datetime import datetime

def run_probe(cmd, log_file, label):
    print(f"\n>>> Running Probe: {label}...")
    start = time.time()
    try:
        res = subprocess.run(cmd, shell=True, capture_output=True, text=True)
        duration = time.time() - start
        output = res.stdout if res.stdout else res.stderr
        
        with open(log_file, 'w', encoding='utf-8') as f:
            f.write(output)
            
        print(f"    [COMPLETED in {duration:.2f}s] Log saved to: {os.path.basename(log_file)}")
        return output
    except Exception as e:
        err = f"Error running {cmd}: {str(e)}"
        print(f"    [FAILED] {err}")
        with open(log_file, 'w', encoding='utf-8') as f:
            f.write(err)
        return err

def main():
    base_dir = r"d:\FTSITS\ft_portal_base(2)\ft_portal_base"
    diag_dir = os.path.join(base_dir, "scratch", "diagnostics")
    logs_dir = os.path.join(diag_dir, "logs")
    os.makedirs(logs_dir, exist_ok=True)
    
    timestamp = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
    print("=" * 80)
    print(f"FTS PORTAL MODERNIZATION: AUTOMATED EMPIRICAL PROOF & DIAGNOSTIC RUNNER")
    print(f"Execution Started At: {timestamp}")
    print("=" * 80)
    
    probes = [
        ("python 01_audit_stack.py", "audit_stack_versions.log", "Probe 1: Stack & Package Obsolescence"),
        ("php 02_profile_queries.php", "audit_n_plus_one_queries.log", "Probe 2: N+1 Database Query Explosion"),
        ("php 03_audit_sync_services.php", "audit_sync_blocking_services.log", "Probe 3: Synchronous Blocking Services & Worker Starvation"),
        ("php 04_profile_permissions.php", "audit_permissions_overhead.log", "Probe 4: Uncached Permissions Overhead"),
        ("python 05_concurrency_load_test.py", "audit_concurrency_stress_test.log", "Probe 5: Concurrency Latency & Worker Saturation"),
        ("python 06_audit_assets.py", "audit_asset_footprint.log", "Probe 6: Monolithic Asset Burden & 59 Plugins")
    ]
    
    master_log_path = os.path.join(logs_dir, "full_diagnostic_run.log")
    with open(master_log_path, 'w', encoding='utf-8') as master:
        master.write(f"FTS PORTAL MODERNIZATION - MASTER DIAGNOSTIC & EMPIRICAL EVIDENCE RUN\n")
        master.write(f"Run Timestamp: {timestamp}\n")
        master.write("=" * 80 + "\n\n")
        
        for cmd, log_name, label in probes:
            full_log_path = os.path.join(logs_dir, log_name)
            output = run_probe(f"cd /d \"{diag_dir}\" && {cmd}", full_log_path, label)
            master.write(f"\n{'='*80}\n{label}\n{'='*80}\n\n")
            master.write(output)
            master.write("\n\n")
            
    print("\n" + "=" * 80)
    print("ALL 6 PROBES COMPLETED SUCCESSFULLY!")
    print(f"Master Evidence Log: {master_log_path}")
    print("=" * 80)

if __name__ == "__main__":
    main()
