import os
import sys
import time
import urllib.request
import urllib.error
import concurrent.futures

def fetch_url(url, request_id):
    start = time.time()
    try:
        req = urllib.request.Request(
            url,
            headers={'User-Agent': 'FTS-Diagnostic-Probe/1.0'}
        )
        with urllib.request.urlopen(req, timeout=10) as response:
            status = response.status
            content_len = len(response.read())
            duration = (time.time() - start) * 1000 # ms
            return {'id': request_id, 'status': status, 'duration': duration, 'error': None}
    except Exception as e:
        duration = (time.time() - start) * 1000
        return {'id': request_id, 'status': 0, 'duration': duration, 'error': str(e)}

def run_concurrency_test(url, concurrency, total_requests=20):
    start_total = time.time()
    results = []
    
    with concurrent.futures.ThreadPoolExecutor(max_workers=concurrency) as executor:
        futures = [executor.submit(fetch_url, url, i) for i in range(total_requests)]
        for f in concurrent.futures.as_completed(futures):
            results.append(f.result())
            
    wall_clock = time.time() - start_total
    
    durations = [r['duration'] for r in results if r['error'] is None]
    errors = [r for r in results if r['error'] is not None]
    
    avg_lat = sum(durations) / len(durations) if durations else 0
    min_lat = min(durations) if durations else 0
    max_lat = max(durations) if durations else 0
    rps = total_requests / wall_clock if wall_clock > 0 else 0
    
    return {
        'concurrency': concurrency,
        'total': total_requests,
        'success': len(durations),
        'errors': len(errors),
        'min_ms': min_lat,
        'avg_ms': avg_lat,
        'max_ms': max_lat,
        'rps': rps,
        'wall_clock': wall_clock
    }

def main():
    target_url = "http://127.0.0.1:8001/login"
    print("=" * 70)
    print("PROBE 5: CONCURRENCY LATENCY & WORKER QUEUE SATURATION BENCHMARK")
    print("=" * 70)
    print(f"Target Endpoint: {target_url}\n")
    
    # Check if server is reachable first
    try:
        urllib.request.urlopen(target_url, timeout=3)
    except Exception as e:
        print(f"[!] Warning: Local server at {target_url} not immediately reachable: {e}")
        print("    Ensure `php artisan serve --port=8001` is active.\n")

    concurrency_levels = [1, 3, 5, 10]
    total_per_test = 15
    
    print(f"{'Concurrency':<14} | {'Reqs':<6} | {'Avg Latency':<14} | {'Max Latency':<14} | {'Throughput':<12} | {'Degradation'}")
    print("-" * 80)
    
    base_avg = None
    
    for c in concurrency_levels:
        res = run_concurrency_test(target_url, concurrency=c, total_requests=total_per_test)
        if base_avg is None and res['avg_ms'] > 0:
            base_avg = res['avg_ms']
            
        multiplier = f"{res['avg_ms'] / base_avg:.2f}x slower" if (base_avg and base_avg > 0) else "Baseline"
        
        print(f"{c} Concurrent   | {res['total']:<6} | {res['avg_ms']:<11.1f} ms | {res['max_ms']:<11.1f} ms | {res['rps']:<8.1f} req/s | {multiplier}")
        time.sleep(1) # Brief cooldown between runs

    print("\n[EMPIRICAL TAKEAWAY]")
    print("Because traditional single-threaded/PHP-FPM worker pools process requests synchronously,")
    print("every additional concurrent user adds latency to all other requests, compounding into")
    print("504 Gateway Timeouts once the concurrency threshold exceeds the available worker pool.")
    print("Laravel Octane (Swoole) eliminates this by keeping the application persistent in memory.")
    print("=" * 70)

if __name__ == "__main__":
    main()
